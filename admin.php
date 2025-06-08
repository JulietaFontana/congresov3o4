<?php
session_start();
if (!isset($_SESSION['roles']) || !in_array('admin', $_SESSION['roles'])) {
    header("Location: login.php");
    exit();
}

$hoy = date('Y-m-d');
$inicio_congreso = '2025-06-10';
$fin_congreso = '2025-06-12';
$mostrar_qr = ($hoy >= $inicio_congreso && $hoy <= $fin_congreso);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Panel Admin</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h2>Panel del Administrador</h2>
        <p>Bienvenido, <?php echo $_SESSION['nombre'] . ' ' . $_SESSION['apellido']; ?></p>
        <a href="logout.php" class="btn">Cerrar sesión</a>
    </div>
</body>
</html>
