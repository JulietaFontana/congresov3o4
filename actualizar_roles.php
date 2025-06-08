<?php
session_start();
require 'db.php';

if (!isset($_SESSION['roles']) || !in_array('admin', $_SESSION['roles'])) {
    die("Acceso denegado");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['roles'])) {
    foreach ($_POST['roles'] as $email => $roles) {
        // Buscar el ID del usuario
        $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $res = $stmt->get_result();
        $user = $res->fetch_assoc();

        if (!$user) continue;

        $id_usuario = $user['id'];

        // Eliminar roles actuales
        $conn->query("DELETE FROM usuario_roles WHERE id_usuario = $id_usuario");

        // Insertar nuevos roles
        $insert = $conn->prepare("INSERT INTO usuario_roles (id_usuario, id_rol) VALUES (?, ?)");
        foreach ($roles as $rol_id) {
            $insert->bind_param("ii", $id_usuario, $rol_id);
            $insert->execute();
        }
    }

    header("Location: gestionar_usuarios.php");
    exit();
} else {
    die("No se recibieron datos de roles");
}
