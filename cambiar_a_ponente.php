<?php
session_start();
require 'db.php';

if (!isset($_SESSION['id'])) {
    http_response_code(403);
    exit;
}

$id_usuario = $_SESSION['id'];

// Obtener el ID del rol "ponente"
$sql_rol = "SELECT id FROM roles WHERE nombre = 'ponente'";
$res_rol = $conn->query($sql_rol);

if ($res_rol && $rol = $res_rol->fetch_assoc()) {
    $id_rol = $rol['id'];

    // Verificar si ya lo tiene
    $check_sql = "SELECT * FROM usuario_roles WHERE id_usuario = ? AND id_rol = ?";
    $stmt_check = $conn->prepare($check_sql);
    $stmt_check->bind_param("ii", $id_usuario, $id_rol);
    $stmt_check->execute();
    $check_result = $stmt_check->get_result();

    if ($check_result->num_rows === 0) {
        // Insertar si no lo tiene
        $insert_sql = "INSERT INTO usuario_roles (id_usuario, id_rol) VALUES (?, ?)";
        $stmt_insert = $conn->prepare($insert_sql);
        $stmt_insert->bind_param("ii", $id_usuario, $id_rol);
        $stmt_insert->execute();
    }

    // Actualizar la sesión si no estaba
    if (!in_array('ponente', $_SESSION['roles'])) {
        $_SESSION['roles'][] = 'ponente';
    }

    http_response_code(200);
} else {
    http_response_code(500); // error si no encuentra el rol
}
