<?php
session_start();

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'ponente') {
    die("Acceso denegado.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['ponencia'])) {
    $archivo = $_FILES['ponencia'];
    
    if ($archivo['type'] !== 'application/pdf') {
        die("Solo se permiten archivos PDF.");
    }

    $nombreFinal = "ponencia_" . $_SESSION['username'] . "_" . time() . ".pdf";
    $rutaDestino = "ponencias/" . $nombreFinal;

    if (move_uploaded_file($archivo['tmp_name'], $rutaDestino)) {
        echo "<p style='text-align:center; padding: 40px;'>✅ Ponencia subida con éxito. <a href='index.php'>Volver al inicio</a></p>";
    } else {
        echo "❌ Error al subir el archivo.";
    }
} else {
    echo "❌ No se recibió el archivo.";
}
?>
