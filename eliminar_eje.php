<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['roles']) || !in_array('admin', $_SESSION['roles'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = (int) $_GET['id'];

    $stmt = $conn->prepare("DELETE FROM ejes WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

header("Location: gestionar_ejes.php");
exit();
?>
