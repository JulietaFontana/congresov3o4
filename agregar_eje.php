<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['roles']) || !in_array('admin', $_SESSION['roles'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['nombre'])) {
    $nombre = trim($_POST['nombre']);
    
    // Validar que el eje no exista ya
    $stmt = $conn->prepare("SELECT COUNT(*) FROM ejes WHERE nombre = ?");
    $stmt->bind_param("s", $nombre);
    $stmt->execute();
    $stmt->bind_result($existe);
    $stmt->fetch();
    $stmt->close();

    if ($existe == 0) {
        $insert = $conn->prepare("INSERT INTO ejes (nombre) VALUES (?)");
        $insert->bind_param("s", $nombre);
        $insert->execute();
        $insert->close();
    }
}

header("Location: gestionar_ejes.php");
exit();
?>
