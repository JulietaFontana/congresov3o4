<?php
require 'db.php';

$nombre = $_POST['nombre'];
$apellido = $_POST['apellido'];
$dni = $_POST['dni'];
$email = $_POST['email'];
$telefono = $_POST['telefono'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);

// Insertar en la tabla usuarios (sin el campo 'rol')
$sql = "INSERT INTO usuarios (username, password, nombre, apellido, email, telefono, dni) VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssssss", $email, $password, $nombre, $apellido, $email, $telefono, $dni);

if ($stmt->execute()) {
    // Obtener el ID del nuevo usuario
    $id_usuario = $stmt->insert_id;

    // Asignar el rol 'user' por defecto (id_rol = 3 asumiendo el orden de inserción)
    $rol_stmt = $conn->prepare("INSERT INTO usuario_roles (id_usuario, id_rol) VALUES (?, ?)");
    $rol_user_id = 3; // ⚠️ Asegurate que este sea el id del rol 'user'
    $rol_stmt->bind_param("ii", $id_usuario, $rol_user_id);
    $rol_stmt->execute();

    header("Location: login.php");
} else {
    echo "Error: " . $stmt->error;
}
?>
