<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['roles']) || !in_array('evaluador', $_SESSION['roles'])) {
    die("⛔ Acceso denegado.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_ponencia'], $_POST['evaluacion'], $_POST['estado'])) {
    $id_ponencia = (int) $_POST['id_ponencia'];
    $id_evaluador = $_SESSION['id'];
    $evaluacion = trim($_POST['evaluacion']);
    $estado = $_POST['estado'] === 'desaprobada' ? 'desaprobada' : 'aprobada';

    // 1. Guardar evaluación y estado
    $stmt = $conn->prepare("UPDATE ponencia_evaluador SET evaluacion = ?, estado = ? WHERE id_ponencia = ? AND id_evaluador = ?");
    $stmt->bind_param("ssii", $evaluacion, $estado, $id_ponencia, $id_evaluador);
    $stmt->execute();
    $stmt->close();

    // 2. Marcar ponencia como evaluada
    $stmt = $conn->prepare("UPDATE ponencias SET fue_evaluada = 1 WHERE id = ?");
    $stmt->bind_param("i", $id_ponencia);
    $stmt->execute();
    $stmt->close();

    // 3. Verificar si ya hay 2 evaluaciones y si están en conflicto
    $stmt = $conn->prepare("SELECT id_evaluador, estado FROM ponencia_evaluador WHERE id_ponencia = ? AND orden IN (1,2) AND estado IS NOT NULL");
    $stmt->bind_param("i", $id_ponencia);
    $stmt->execute();
    $res = $stmt->get_result();
    $evaluaciones = $res->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    if (count($evaluaciones) === 2 && $evaluaciones[0]['estado'] !== $evaluaciones[1]['estado']) {
        // 4. Verificar que aún no haya un tercer evaluador asignado
        $stmt = $conn->prepare("SELECT COUNT(*) FROM ponencia_evaluador WHERE id_ponencia = ? AND orden = 3");
        $stmt->bind_param("i", $id_ponencia);
        $stmt->execute();
        $stmt->bind_result($hay_tercero);
        $stmt->fetch();
        $stmt->close();

        if ($hay_tercero == 0) {
            // 5. Obtener datos de la ponencia: autor, eje y colaboradores
            $stmt = $conn->prepare("SELECT id_usuario, id_eje, autores_colaboradores FROM ponencias WHERE id = ?");
            $stmt->bind_param("i", $id_ponencia);
            $stmt->execute();
            $stmt->bind_result($id_autor, $id_eje, $colaboradores_raw);
            $stmt->fetch();
            $stmt->close();

            // 6. Parsear correos de colaboradores
            $correos_colaboradores = array_filter(array_map('trim', explode("\n", $colaboradores_raw)));
            $ids_colaboradores = [];

            if (!empty($correos_colaboradores)) {
                $placeholders = implode(',', array_fill(0, count($correos_colaboradores), '?'));
                $tipos = str_repeat('s', count($correos_colaboradores));

                $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email IN ($placeholders)");
                $stmt->bind_param($tipos, ...$correos_colaboradores);
                $stmt->execute();
                $res = $stmt->get_result();
                while ($row = $res->fetch_assoc()) {
                    $ids_colaboradores[] = $row['id'];
                }
                $stmt->close();
            }

            // 7. Excluir autor, colaboradores y evaluadores 1 y 2
            $excluir_ids = array_merge([$id_autor], array_column($evaluaciones, 'id_evaluador'), $ids_colaboradores);
            $placeholders = implode(',', array_fill(0, count($excluir_ids), '?'));
            $tipos = str_repeat('i', count($excluir_ids));

            // 8. Buscar evaluadores válidos del mismo eje
            $sql = "
                SELECT u.id FROM usuarios u
                JOIN usuario_roles ur ON u.id = ur.id_usuario
                JOIN roles r ON ur.id_rol = r.id
                JOIN evaluador_eje ee ON u.id = ee.id_usuario
                WHERE r.nombre = 'evaluador' AND ee.id_eje = ? AND u.id NOT IN ($placeholders)
            ";
            $stmt = $conn->prepare($sql);
            $bind_types = 'i' . $tipos;
            $stmt->bind_param($bind_types, $id_eje, ...$excluir_ids);
            $stmt->execute();
            $res = $stmt->get_result();
            $posibles = [];
            while ($row = $res->fetch_assoc()) {
                $posibles[] = $row['id'];
            }
            $stmt->close();

            // 9. Asignar tercer evaluador
            if (!empty($posibles)) {
                shuffle($posibles);
                $tercero = $posibles[0];
                $stmt = $conn->prepare("INSERT INTO ponencia_evaluador (id_ponencia, id_evaluador, orden) VALUES (?, ?, 3)");
                $stmt->bind_param("ii", $id_ponencia, $tercero);
                $stmt->execute();
                $stmt->close();
            }
        }
    }

    header("Location: evaluar_ponencias.php");
    exit();
} else {
    echo "❌ Datos incompletos.";
}
