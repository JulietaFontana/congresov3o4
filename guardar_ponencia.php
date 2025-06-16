<?php
session_start();
require_once 'db.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'vendor/autoload.php';
use setasign\Fpdi\Fpdi;

if (!isset($_SESSION['roles']) || !in_array('ponente', $_SESSION['roles'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Acceso denegado. Esta sección es solo para ponentes.']);
    exit();
}

if (
    $_SERVER['REQUEST_METHOD'] == 'POST' &&
    isset($_FILES['ponencia']) &&
    isset($_POST['id_eje'], $_POST['universidad'], $_POST['autores'], $_POST['palabras_clave'], $_POST['resumen'])
) {
    $archivo = $_FILES['ponencia'];
    $id_eje = (int) $_POST['id_eje'];
    $id_usuario = $_SESSION['id'];
    $universidad = trim($_POST['universidad']);
    $autores = trim($_POST['autores']);
    $palabras_clave = trim($_POST['palabras_clave']);
    $resumen = trim($_POST['resumen']);

    if ($archivo['type'] !== 'application/pdf') {
        echo json_encode(['success' => false, 'message' => '❌ Solo se permiten archivos PDF.']);
        exit();
    }

    $usuario = isset($_SESSION['nombre'], $_SESSION['apellido']) 
        ? $_SESSION['nombre'] . '_' . $_SESSION['apellido']
        : 'usuario_desconocido';

    $codigoAnonimo = substr(md5(uniqid('', true)), 0, 8); // 8 caracteres aleatorios
    $nombreFinal = "ponencia_" . $codigoAnonimo . ".pdf";

    $rutaDestino = "ponencias/" . $nombreFinal;

    if (!file_exists("ponencias")) {
        mkdir("ponencias", 0777, true);
    }

    if (move_uploaded_file($archivo['tmp_name'], $rutaDestino)) {
        // Insertar ponencia
        $stmt = $conn->prepare("
            INSERT INTO ponencias 
            (id_usuario, id_eje, archivo, fecha_subida, universidad, autores_colaboradores, palabras_clave, resumen) 
            VALUES (?, ?, ?, NOW(), ?, ?, ?, ?)
        ");
        $stmt->bind_param("iisssss", $id_usuario, $id_eje, $nombreFinal, $universidad, $autores, $palabras_clave, $resumen);
        $stmt->execute();
        $id_ponencia = $stmt->insert_id;
        $stmt->close();

        // Extraer correos de colaboradores (uno por línea)
        $correos_colaboradores = array_filter(array_map('trim', explode("\n", $autores)));

        // Buscar IDs de usuarios que coincidan con esos correos
        $ids_colaboradores = [];
        if (!empty($correos_colaboradores)) {
            $placeholders = implode(',', array_fill(0, count($correos_colaboradores), '?'));
            $tipos = str_repeat('s', count($correos_colaboradores));

            $stmt_colab = $conn->prepare("SELECT id FROM usuarios WHERE email IN ($placeholders)");
            $stmt_colab->bind_param($tipos, ...$correos_colaboradores);
            $stmt_colab->execute();
            $res_colab = $stmt_colab->get_result();
            while ($row = $res_colab->fetch_assoc()) {
                $ids_colaboradores[] = $row['id'];
            }
            $stmt_colab->close();
        }

        // Preparar lista de exclusión (autor + colaboradores)
        $excluir_ids = array_merge([$id_usuario], $ids_colaboradores);
        $placeholders = implode(',', array_fill(0, count($excluir_ids), '?'));
        $tipos = str_repeat('i', count($excluir_ids));

        // Obtener evaluadores válidos (que no sean autor ni colaboradores)
        $stmt_e = $conn->prepare("
            SELECT u.id FROM usuarios u
            JOIN usuario_roles ur ON u.id = ur.id_usuario
            JOIN roles r ON ur.id_rol = r.id
            WHERE r.nombre = 'evaluador' AND u.id NOT IN ($placeholders)
        ");
        $stmt_e->bind_param($tipos, ...$excluir_ids);
        $stmt_e->execute();
        $evaluadores_result = $stmt_e->get_result();
        $evaluadores = [];
        while ($row = $evaluadores_result->fetch_assoc()) {
            $evaluadores[] = $row['id'];
        }
        $stmt_e->close();

        if (count($evaluadores) >= 2) {
            shuffle($evaluadores);
            $evaluador1 = $evaluadores[0];
            $evaluador2 = $evaluadores[1];

            // Crear PDF sin la primera página
            $pdf = new Fpdi();
            $pageCount = $pdf->setSourceFile($rutaDestino);
            for ($i = 2; $i <= $pageCount; $i++) {
                $templateId = $pdf->importPage($i);
                $pdf->AddPage();
                $pdf->useTemplate($templateId);
            }
            $archivoEvaluador = "ponencias/evaluacion_" . $codigoAnonimo . ".pdf";
            $pdf->Output("F", $archivoEvaluador);

            // Insertar evaluador 1
            $stmt = $conn->prepare("INSERT INTO ponencia_evaluador (id_ponencia, id_evaluador, orden) VALUES (?, ?, 1)");
            $stmt->bind_param("ii", $id_ponencia, $evaluador1);
            $stmt->execute();
            $stmt->close();

            // Insertar evaluador 2
            $stmt = $conn->prepare("INSERT INTO ponencia_evaluador (id_ponencia, id_evaluador, orden) VALUES (?, ?, 2)");
            $stmt->bind_param("ii", $id_ponencia, $evaluador2);
            $stmt->execute();
            $stmt->close();
        }

        echo json_encode(['success' => true, 'message' => '✅ Ponencia subida con éxito.']);
    } else {
        echo json_encode(['success' => false, 'message' => '❌ Error al subir el archivo.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => '❌ Faltan datos del formulario.']);
}
?>
