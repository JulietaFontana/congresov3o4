<?php
session_start();
require 'db.php';

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

$sql = "SELECT * FROM usuarios WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$res = $stmt->get_result();
$user = $res->fetch_assoc();

if ($user && password_verify($password, $user['password'])) {
    $_SESSION['nombre'] = $user['nombre'];
    $_SESSION['apellido'] = $user['apellido'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['id'] = $user['id'];

    // Obtener todos los roles
    $roles_sql = "
        SELECT r.nombre 
        FROM usuario_roles ur
        JOIN roles r ON ur.id_rol = r.id
        WHERE ur.id_usuario = ?
    ";
    $roles_stmt = $conn->prepare($roles_sql);
    $roles_stmt->bind_param("i", $user['id']);
    $roles_stmt->execute();
    $roles_result = $roles_stmt->get_result();

    $roles = [];
    while ($row = $roles_result->fetch_assoc()) {
        $roles[] = $row['nombre'];
    }

    $_SESSION['roles'] = $roles;

    header("Location: index.php");
    exit;
} else {
    $_SESSION['login_error'] = "Correo o contraseña inválidos.";
    header("Location: login.php");
    exit;
}
?>
