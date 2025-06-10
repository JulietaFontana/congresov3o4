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

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['ponencia']) && isset($_POST['id_eje'])) {
    $archivo = $_FILES['ponencia'];
    $id_eje = (int) $_POST['id_eje'];
    $id_usuario = $_SESSION['id'];

    if ($archivo['type'] !== 'application/pdf') {
        echo json_encode(['success' => false, 'message' => '❌ Solo se permiten archivos PDF.']);
        exit();
    }

    $usuario = isset($_SESSION['nombre'], $_SESSION['apellido']) 
        ? $_SESSION['nombre'] . '_' . $_SESSION['apellido']
        : 'usuario_desconocido';

    $nombreFinal = "ponencia_" . preg_replace('/\s+/', '_', $usuario) . "_" . time() . ".pdf";
    $rutaDestino = "ponencias/" . $nombreFinal;

    if (!file_exists("ponencias")) {
        mkdir("ponencias", 0777, true);
    }

    if (move_uploaded_file($archivo['tmp_name'], $rutaDestino)) {
        $stmt = $conn->prepare("INSERT INTO ponencias (id_usuario, id_eje, archivo, fecha_subida) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("iis", $id_usuario, $id_eje, $nombreFinal);
        $stmt->execute();
        $id_ponencia = $stmt->insert_id;
        $stmt->close();

        // Obtener evaluadores disponibles (excepto el usuario actual)
        $evaluadores_sql = "
            SELECT u.id FROM usuarios u
            JOIN usuario_roles ur ON u.id = ur.id_usuario
            JOIN roles r ON ur.id_rol = r.id
            WHERE r.nombre = 'evaluador' AND u.id != ?
        ";

        $stmt_e = $conn->prepare($evaluadores_sql);
        $stmt_e->bind_param("i", $id_usuario);
        $stmt_e->execute();
        $evaluadores_result = $stmt_e->get_result();
        $evaluadores = [];
        while ($row = $evaluadores_result->fetch_assoc()) {
            $evaluadores[] = $row['id'];
        }
        $stmt_e->close();

        if (!empty($evaluadores)) {
            $evaluador_aleatorio = $evaluadores[array_rand($evaluadores)];

            // Crear PDF sin la primera página
            $pdf = new Fpdi();
            $pageCount = $pdf->setSourceFile($rutaDestino);
            for ($i = 2; $i <= $pageCount; $i++) {
                $templateId = $pdf->importPage($i);
                $pdf->AddPage();
                $pdf->useTemplate($templateId);
            }
            $archivoEvaluador = "ponencias/evaluacion_" . $nombreFinal;
            $pdf->Output("F", $archivoEvaluador);

            // Asignar evaluador
            $stmt = $conn->prepare("INSERT INTO ponencia_evaluador (id_ponencia, id_evaluador) VALUES (?, ?)");
            $stmt->bind_param("ii", $id_ponencia, $evaluador_aleatorio);
            $stmt->execute();
            $stmt->close();
        }

        echo json_encode(['success' => true, 'message' => '✅ Ponencia subida con éxito.']);
    } else {
        echo json_encode(['success' => false, 'message' => '❌ Error al subir el archivo.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => '❌ No se recibió el archivo o no se seleccionó un eje temático.']);
}
?>
