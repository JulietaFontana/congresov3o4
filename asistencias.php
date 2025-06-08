<?php
session_start();
require 'db.php';

if (!isset($_SESSION['email'])) {
    echo "⚠️ Debés iniciar sesión para registrar tu asistencia.";
    exit;
}

$id_evento = $_GET['id'] ?? 1;
$email = $_SESSION['email'];

$stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$res = $stmt->get_result();
$user = $res->fetch_assoc();

if (!$user) {
    echo "⚠️ Usuario no encontrado.";
    exit;
}

$id_usuario = $user['id'];

$sql = "INSERT IGNORE INTO asistencias (id_usuario, id_evento) VALUES (?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $id_usuario, $id_evento);
$stmt->execute();

echo "<h2>✅ Asistencia registrada correctamente</h2>";
echo "<p>Gracias por participar del Congreso Nacional de Gestores Tecnológicos.</p>";
?>
