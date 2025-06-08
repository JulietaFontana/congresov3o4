<?php
session_start();
require 'db.php';

// Verificación de acceso solo para admins
if (!isset($_SESSION['roles']) || !in_array('admin', $_SESSION['roles'])) {
    header("Location: index.php");
    exit();
}

// Si se recibió el formulario para cambiar el rol
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $nuevo_rol = $_POST['nuevo_rol'];

    // Obtener el id del usuario
    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();
    $usuario = $res->fetch_assoc();

    if (!$usuario) {
        echo "❌ Usuario no encontrado.";
        exit();
    }

    $id_usuario = $usuario['id'];

    // Obtener el id del rol a asignar
    $rol_stmt = $conn->prepare("SELECT id FROM roles WHERE nombre = ?");
    $rol_stmt->bind_param("s", $nuevo_rol);
    $rol_stmt->execute();
    $rol_res = $rol_stmt->get_result();
    $rol = $rol_res->fetch_assoc();

    if (!$rol) {
        echo "❌ Rol no válido.";
        exit();
    }

    $id_rol = $rol['id'];

    // Verificar si ya tiene ese rol
    $check_stmt = $conn->prepare("SELECT * FROM usuario_roles WHERE id_usuario = ? AND id_rol = ?");
    $check_stmt->bind_param("ii", $id_usuario, $id_rol);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        echo "⚠️ El usuario ya tiene este rol.";
        exit();
    }

    // Insertar el nuevo rol
    $insert_stmt = $conn->prepare("INSERT INTO usuario_roles (id_usuario, id_rol) VALUES (?, ?)");
    $insert_stmt->bind_param("ii", $id_usuario, $id_rol);
    
    if ($insert_stmt->execute()) {
        header("Location: gestionar_usuarios.php");
        exit();
    } else {
        echo "❌ Error al asignar el rol.";
    }
}
?>
