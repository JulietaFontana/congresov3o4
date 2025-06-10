<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['roles']) || !in_array('evaluador', $_SESSION['roles'])) {
    die("⛔ Acceso denegado.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_ponencia'], $_POST['evaluacion'], $_POST['estado'])) {
    $id_ponencia = (int) $_POST['id_ponencia'];
    $id_evaluador = $_SESSION['id'];
    $evaluacion = trim($_POST['evaluacion']);
    $estado = $_POST['estado'] === 'desaprobada' ? 'desaprobada' : 'aprobada';

    // Guardar evaluación y estado
    $stmt = $conn->prepare("UPDATE ponencia_evaluador SET evaluacion = ?, estado = ? WHERE id_ponencia = ? AND id_evaluador = ?");
    $stmt->bind_param("ssii", $evaluacion, $estado, $id_ponencia, $id_evaluador);
    $stmt->execute();
    $stmt->close();

    // Marcar ponencia como evaluada
    $stmt2 = $conn->prepare("UPDATE ponencias SET fue_evaluada = 1 WHERE id = ?");
    $stmt2->bind_param("i", $id_ponencia);
    $stmt2->execute();
    $stmt2->close();

    header("Location: evaluar_ponencias.php");
    exit();
} else {
    echo "❌ Datos incompletos.";
}
