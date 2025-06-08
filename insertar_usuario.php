<?php
require 'db.php';

$nombre = $_POST['nombre'];
$apellido = $_POST['apellido'];
$dni = $_POST['dni'];
$email = $_POST['email'];
$telefono = $_POST['telefono'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
$rol = 'user';  // Automático

$sql = "INSERT INTO usuarios (username, password, rol, nombre, apellido, email, telefono, dni) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssssss", $email, $password, $rol, $nombre, $apellido, $email, $telefono, $dni);

if ($stmt->execute()) {
    header("Location: login.php");
} else {
    echo "Error: " . $stmt->error;
}
?>