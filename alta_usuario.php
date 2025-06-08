<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['roles']) || !in_array('admin', $_SESSION['roles'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $dni = $_POST['dni'];
    $telefono = $_POST['telefono'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $roles = $_POST['roles'] ?? [];

    $stmt = $conn->prepare("INSERT INTO usuarios (username, password, nombre, apellido, dni, email, telefono) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $username, $password, $nombre, $apellido, $dni, $email, $telefono);
    
    if ($stmt->execute()) {
        $id_usuario = $conn->insert_id;

        foreach ($roles as $id_rol) {
            $stmt = $conn->prepare("INSERT INTO usuario_roles (id_usuario, id_rol) VALUES (?, ?)");
            $stmt->bind_param("ii", $id_usuario, $id_rol);
            $stmt->execute();
        }
    }

    header("Location: gestionar_usuarios.php");
    exit();
}
