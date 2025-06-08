<?php
$host = "localhost";
$user = "congreso_user";
$pass = "password123";
$db = "congreso";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
?>