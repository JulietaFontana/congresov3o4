<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'ponente') {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel del Ponente</title>
    <link rel="stylesheet" href="styles.css">
    <script src="js.js"></script>
</head>
<body>

<?php include 'header.php'; ?>

<main>
  <section>
    <div class="container">
        <h2>Panel del Ponente</h2>
        <p>Bienvenido, <?php echo $_SESSION['username']; ?>.</p>

        <a href="subir_ponencia.php" class="btn" style="margin-top: 20px; display:inline-block;">
            📄 Subir tu ponencia
        </a>
    </div>
  </section>
</main>

<?php include 'footer.php'; ?>
<script src="js.js"></script>

</body>
</html>
