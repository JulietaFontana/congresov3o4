<?php
session_start();
require 'db.php';

if (!in_array('admin', $_SESSION['roles'] ?? [])) {
    die("Acceso denegado");
}

if (!empty($_POST['asistencias'])) {
    foreach ($_POST['asistencias'] as $id_usuario => $dias) {
        foreach ($dias as $fecha => $valor) {
            $fecha_sql = $fecha . " 00:00:00";

            // Verificar si ya existe
            $check = $conn->prepare("SELECT id FROM asistencias WHERE id_usuario = ? AND id_evento = 1 AND DATE(fecha_hora) = ?");
            $check->bind_param("is", $id_usuario, $fecha);
            $check->execute();
            $res = $check->get_result();

            if ($res->num_rows == 0) {
                // Insertar si no existe
                $stmt = $conn->prepare("INSERT INTO asistencias (id_usuario, id_evento, fecha_hora) VALUES (?, 1, ?)");
                $stmt->bind_param("is", $id_usuario, $fecha_sql);
                $stmt->execute();
            }
        }
    }
    header("Location: asistencias_manual.php?guardado=1");
    exit;
}
?>
