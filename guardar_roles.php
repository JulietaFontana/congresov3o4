<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['roles']) || !in_array('admin', $_SESSION['roles'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['usuarios'])) {
    foreach ($_POST['usuarios'] as $id_usuario) {
        // 1. Eliminar roles actuales
        $stmt = $conn->prepare("DELETE FROM usuario_roles WHERE id_usuario = ?");
        $stmt->bind_param("i", $id_usuario);
        $stmt->execute();

        // 2. Insertar nuevos roles
        $tiene_rol_evaluador = false;
        if (isset($_POST['roles'][$id_usuario])) {
            foreach ($_POST['roles'][$id_usuario] as $id_rol) {
                $stmt = $conn->prepare("INSERT INTO usuario_roles (id_usuario, id_rol) VALUES (?, ?)");
                $stmt->bind_param("ii", $id_usuario, $id_rol);
                $stmt->execute();

                // Verificamos si tiene el rol de evaluador
                $stmt_check = $conn->prepare("SELECT nombre FROM roles WHERE id = ?");
                $stmt_check->bind_param("i", $id_rol);
                $stmt_check->execute();
                $stmt_check->bind_result($nombre_rol);
                $stmt_check->fetch();
                if ($nombre_rol === 'evaluador') {
                    $tiene_rol_evaluador = true;
                }
                $stmt_check->close();
            }
        }

        // 3. Actualizar ejes temáticos si es evaluador
        $stmt = $conn->prepare("DELETE FROM evaluador_eje WHERE id_usuario = ?");
        $stmt->bind_param("i", $id_usuario);
        $stmt->execute();

        if ($tiene_rol_evaluador && isset($_POST['ejes'][$id_usuario])) {
            foreach ($_POST['ejes'][$id_usuario] as $id_eje) {
                $stmt = $conn->prepare("INSERT INTO evaluador_eje (id_usuario, id_eje) VALUES (?, ?)");
                $stmt->bind_param("ii", $id_usuario, $id_eje);
                $stmt->execute();
            }
        }
    }
}

header("Location: gestionar_usuarios.php");
exit();
