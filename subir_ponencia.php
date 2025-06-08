<?php
session_start();
require_once 'db.php'; // 🔁 Agregado para poder consultar ejes

if (!isset($_SESSION['roles']) || !in_array('ponente', $_SESSION['roles'])) {
    header("Location: index.php");
    exit();
}

// Consulta de ejes temáticos
$ejes_resultado = $conn->query("SELECT id, nombre FROM ejes");
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
            <label for="eje">Seleccioná el eje temático:</label>
            <select name="id_eje" required>
                <option value="" disabled selected>-- Seleccioná un eje --</option>
                <?php while ($eje = $ejes_resultado->fetch_assoc()): ?>
                    <option value="<?= $eje['id'] ?>"><?= htmlspecialchars($eje['nombre']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>

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
