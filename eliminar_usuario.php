<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['roles']) || !in_array('admin', $_SESSION['roles'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];

    // Eliminar sus roles
    $stmt = $conn->prepare("DELETE FROM usuario_roles WHERE id_usuario = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    // Eliminar el usuario
    $stmt = $conn->prepare("DELETE FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
}

header("Location: gestionar_usuarios.php");
exit();
