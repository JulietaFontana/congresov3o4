<?php
session_start();
require 'db.php';

if (isset($_SESSION['email'])) {
    $email = $_SESSION['email'];
    $stmt = $conn->prepare("UPDATE notificaciones SET leida = 1 WHERE usuario_email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false]);
}
?>