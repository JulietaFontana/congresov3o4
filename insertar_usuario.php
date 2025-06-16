<?php
require 'db.php';

// Obtener los datos del formulario
$nombre = $_POST['nombre'];
$apellido = $_POST['apellido'];
$dni = $_POST['dni'];
$email = $_POST['email'];
$cod_area = $_POST['cod_area'];
$telefono = $_POST['telefono'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);

// Concatenar código de área + teléfono
$telefono_completo = $cod_area . $telefono;

// Insertar nuevo usuario
$sql = "INSERT INTO usuarios (username, password, nombre, apellido, email, telefono, dni) 
        VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssssss", $email, $password, $nombre, $apellido, $email, $telefono_completo, $dni);

if ($stmt->execute()) {
    header("Location: login.php");
    exit;
} else {
    echo "Error: " . $stmt->error;
}
?>
