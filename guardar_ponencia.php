<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['roles']) || !in_array('ponente', $_SESSION['roles'])) {
    die("⛔ Acceso denegado. Esta sección es solo para ponentes.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['ponencia']) && isset($_POST['id_eje'])) {
    $archivo = $_FILES['ponencia'];
    $id_eje = (int) $_POST['id_eje'];
    $id_usuario = $_SESSION['id']; // Asegurate de tener el id guardado en $_SESSION al iniciar sesión

    if ($archivo['type'] !== 'application/pdf') {
        die("❌ Solo se permiten archivos PDF.");
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
        // Guardar en la base de datos
        $stmt = $conn->prepare("INSERT INTO ponencias (id_usuario, id_eje, archivo, fecha_subida) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("iis", $id_usuario, $id_eje, $nombreFinal);
        $stmt->execute();
        $stmt->close();

        echo "<p style='text-align:center; padding: 40px;'>✅ Ponencia subida con éxito. <a href='index.php'>Volver al inicio</a></p>";
    } else {
        echo "❌ Error al subir el archivo.";
    }
} else {
    echo "❌ No se recibió el archivo o no se seleccionó un eje temático.";
}
?>
