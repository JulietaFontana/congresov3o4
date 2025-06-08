<?php
require 'db.php';

$token = bin2hex(random_bytes(10)); // token de 20 caracteres

// Guardarlo en la base de datos
$stmt = $conn->prepare("INSERT INTO qr_tokens (token, id_evento, valido) VALUES (?, 1, 1)");
$stmt->bind_param("s", $token);
$stmt->execute();

echo $token;
