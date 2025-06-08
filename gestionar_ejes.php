<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['roles']) || !in_array('admin', $_SESSION['roles'])) {
    header("Location: login.php");
    exit();
}

$ejes_result = $conn->query("SELECT * FROM ejes ORDER BY nombre");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Ejes Temáticos</title>
    <link rel="stylesheet" href="styles.css">
    <script>
      function toggleNuevoEje() {
        const form = document.getElementById('nuevoEjeForm');
        form.style.display = (form.style.display === 'block') ? 'none' : 'block';
      }
    </script>
</head>
<body>
<?php include 'header.php'; ?>
<main>
    <div class="container">
        <h2>📚 Gestión de Ejes Temáticos</h2>

        <?php while ($eje = $ejes_result->fetch_assoc()): ?>
            <div class="usuario-card">
                <div class="usuario-info">
                    <p><strong><?= htmlspecialchars($eje['nombre']) ?></strong></p>
                </div>
                <div class="acciones">
                    <a href="eliminar_eje.php?id=<?= $eje['id'] ?>" onclick="return confirm('¿Eliminar este eje?')">
                        🗑️
                    </a>
                </div>
            </div>
        <?php endwhile; ?>

        <div class="botones-finales">
            <button type="button" onclick="toggleNuevoEje()" class="btn-principal">➕ Agregar nuevo eje</button>
        </div>

        <form method="POST" action="agregar_eje.php" class="nuevo-usuario-form" id="nuevoEjeForm">
            <h3>Nuevo Eje</h3>
            <input type="text" name="nombre" placeholder="Nombre del eje" required>
            <button type="submit" class="btn-principal">Guardar eje</button>
        </form>
    </div>
</main>
<?php include 'footer.php'; ?>
</body>
</html>
