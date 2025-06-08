<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: index.php");
    exit();
}

require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $nuevo_rol = $_POST['nuevo_rol'];
    $stmt = $conn->prepare("UPDATE usuarios SET rol = ? WHERE email = ?");
    $stmt->bind_param("ss", $nuevo_rol, $email);
    if ($stmt->execute()) {
        header("Location: gestionar_usuarios.php");
    } else {
        echo "Error al actualizar rol.";
    }
}
?>