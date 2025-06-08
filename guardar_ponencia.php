<?php
session_start();

// Validar sesión y rol 'ponente'
if (!isset($_SESSION['roles']) || !in_array('ponente', $_SESSION['roles'])) {
    die("⛔ Acceso denegado. Esta sección es solo para ponentes.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['ponencia'])) {
    $archivo = $_FILES['ponencia'];

    if ($archivo['type'] !== 'application/pdf') {
        die("❌ Solo se permiten archivos PDF.");
    }

    // Usamos nombre y apellido como parte del nombre del archivo
    $usuario = isset($_SESSION['nombre'], $_SESSION['apellido']) 
        ? $_SESSION['nombre'] . '_' . $_SESSION['apellido']
        : 'usuario_desconocido';

    $nombreFinal = "ponencia_" . preg_replace('/\s+/', '_', $usuario) . "_" . time() . ".pdf";
    $rutaDestino = "ponencias/" . $nombreFinal;

    if (!file_exists("ponencias")) {
        mkdir("ponencias", 0777, true);
    }

    if (move_uploaded_file($archivo['tmp_name'], $rutaDestino)) {
        echo "<p style='text-align:center; padding: 40px;'>✅ Ponencia subida con éxito. <a href='index.php'>Volver al inicio</a></p>";
    } else {
        echo "❌ Error al subir el archivo.";
    }
} else {
    echo "❌ No se recibió el archivo.";
}
?>
