<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['roles']) || !in_array('admin', $_SESSION['roles'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['usuarios'])) {
    foreach ($_POST['usuarios'] as $id_usuario) {
        // Eliminar roles actuales
        $stmt = $conn->prepare("DELETE FROM usuario_roles WHERE id_usuario = ?");
        $stmt->bind_param("i", $id_usuario);
        $stmt->execute();

        // Insertar nuevos roles si hay
        if (isset($_POST['roles'][$id_usuario])) {
            foreach ($_POST['roles'][$id_usuario] as $id_rol) {
                $stmt = $conn->prepare("INSERT INTO usuario_roles (id_usuario, id_rol) VALUES (?, ?)");
                $stmt->bind_param("ii", $id_usuario, $id_rol);
                $stmt->execute();
            }
        }
    }
}

header("Location: gestionar_usuarios.php");
exit();
