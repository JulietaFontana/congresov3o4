<?php
require_once 'db.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

// Obtener ponencias con 2 evaluaciones conflictivas y sin evaluador 3
$sql = "
    SELECT p.id, p.id_usuario, p.autores_colaboradores
    FROM ponencias p
    JOIN ponencia_evaluador pe1 ON p.id = pe1.id_ponencia AND pe1.orden = 1
    JOIN ponencia_evaluador pe2 ON p.id = pe2.id_ponencia AND pe2.orden = 2
    LEFT JOIN ponencia_evaluador pe3 ON p.id = pe3.id_ponencia AND pe3.orden = 3
    WHERE pe1.estado IS NOT NULL
      AND pe2.estado IS NOT NULL
      AND pe3.id IS NULL
      AND pe1.estado != pe2.estado
";

$res = $conn->query($sql);
$conflictivas = $res->fetch_all(MYSQLI_ASSOC);

foreach ($conflictivas as $fila) {
    $id_ponencia = $fila['id'];
    $id_autor = $fila['id_usuario'];
    $colaboradores_raw = $fila['autores_colaboradores'];

    // 1. Extraer correos de colaboradores
    $correos_colaboradores = array_filter(array_map('trim', explode("\n", $colaboradores_raw)));
    $ids_colaboradores = [];

    if (!empty($correos_colaboradores)) {
        $placeholders = implode(',', array_fill(0, count($correos_colaboradores), '?'));
        $tipos = str_repeat('s', count($correos_colaboradores));

        $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email IN ($placeholders)");
        $stmt->bind_param($tipos, ...$correos_colaboradores);
        $stmt->execute();
        $res_colab = $stmt->get_result();
        while ($row = $res_colab->fetch_assoc()) {
            $ids_colaboradores[] = $row['id'];
        }
        $stmt->close();
    }

    // 2. Obtener evaluadores ya asignados (orden 1 y 2)
    $evaluadores_asignados = [];
    $stmt = $conn->prepare("SELECT id_evaluador FROM ponencia_evaluador WHERE id_ponencia = ? AND orden IN (1,2)");
    $stmt->bind_param("i", $id_ponencia);
    $stmt->execute();
    $res_eval = $stmt->get_result();
    while ($row = $res_eval->fetch_assoc()) {
        $evaluadores_asignados[] = $row['id_evaluador'];
    }
    $stmt->close();

    // 3. Preparar lista de exclusión
    $excluir_ids = array_merge([$id_autor], $evaluadores_asignados, $ids_colaboradores);
    $placeholders = implode(',', array_fill(0, count($excluir_ids), '?'));
    $tipos = str_repeat('i', count($excluir_ids));

    // 4. Buscar evaluadores válidos
    $stmt = $conn->prepare("
        SELECT u.id FROM usuarios u
        JOIN usuario_roles ur ON u.id = ur.id_usuario
        JOIN roles r ON ur.id_rol = r.id
        WHERE r.nombre = 'evaluador' AND u.id NOT IN ($placeholders)
    ");
    $stmt->bind_param($tipos, ...$excluir_ids);
    $stmt->execute();
    $res_eval3 = $stmt->get_result();
    $posibles = [];
    while ($row = $res_eval3->fetch_assoc()) {
        $posibles[] = $row['id'];
    }
    $stmt->close();

    // 5. Asignar tercer evaluador
    if (!empty($posibles)) {
        shuffle($posibles);
        $tercero = $posibles[0];

        $stmt = $conn->prepare("INSERT INTO ponencia_evaluador (id_ponencia, id_evaluador, orden) VALUES (?, ?, 3)");
        $stmt->bind_param("ii", $id_ponencia, $tercero);
        $stmt->execute();
        $stmt->close();

        echo "✅ Asignado tercer evaluador (ID $tercero) a ponencia $id_ponencia<br>";
    } else {
        echo "⚠️ No hay evaluadores disponibles para ponencia $id_ponencia<br>";
    }
}
?>
