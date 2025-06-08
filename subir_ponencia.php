<?php
session_start();

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'ponente') {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Subir Ponencia</title>
    <link rel="stylesheet" href="styles.css">
    <script src="js.js"></script>
</head>
<body>

<?php include 'header.php'; ?>

<main>
<section class="upload-section">
  <div class="container">
    <h2>Subir tu ponencia (PDF)</h2>
    <form method="POST" action="guardar_ponencia.php" enctype="multipart/form-data">
        <div class="form-group">
            <label for="ponencia">Seleccioná tu archivo PDF:</label>
            <input type="file" name="ponencia" accept="application/pdf" required>
        </div>
        <button type="submit" class="btn">Subir ponencia</button>
    </form>
  </div>
</section>
</main>

<?php include 'footer.php'; ?>

</body>
</html>
