<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['roles']) || !in_array('ponente', $_SESSION['roles'])) {
    header("Location: index.php");
    exit();
}

$id_usuario = $_SESSION['id'];

// Obtener ejes temáticos
$ejes_resultado = $conn->query("SELECT id, nombre FROM ejes");

// Obtener todas las ponencias del usuario con el nombre del eje temático
$stmt = $conn->prepare("
    SELECT 
        p.archivo, 
        p.fue_evaluada, 
        pe.evaluacion AS comentario, 
        pe.estado AS estado, 
        e.nombre AS eje_nombre 
    FROM ponencias p
    JOIN ejes e ON p.id_eje = e.id
    LEFT JOIN ponencia_evaluador pe ON p.id = pe.id_ponencia
    WHERE p.id_usuario = ?
");
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$res_ponencia = $stmt->get_result();
$ponencias = $res_ponencia->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Subir Ponencia</title>
    <link rel="stylesheet" href="styles.css">
    <script src="js.js" defer></script>

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

    <?php if (!empty($ponencias)): ?>
      <div class="ponencia-actual">
        <h3>📄 Tus ponencias</h3>
        <?php foreach ($ponencias as $ponencia): ?>
            <div class="ponencia-item">
                <p><strong>Eje temático:</strong> <?= htmlspecialchars($ponencia['eje_nombre']) ?></p>
                <p>Archivo: <a href="ponencias/<?= htmlspecialchars($ponencia['archivo']) ?>" target="_blank">Ver</a></p>
                <p>Estado de evaluación: 
                  <?php if ($ponencia['estado'] === 'aprobada'): ?>
                    <span style="color:green;">✅ Aprobada</span>
                  <?php elseif ($ponencia['estado'] === 'desaprobada'): ?>
                    <span style="color:red;">❌ Desaprobada</span>
                  <?php else: ?>
                    <span>⏳ En espera</span>
                  <?php endif; ?>
                </p>
                <?php if (!empty($ponencia['comentario'])): ?>
                  <div class="comentario-evaluador">
                    <strong>Comentario del evaluador:</strong>
                    <blockquote><?= nl2br(htmlspecialchars($ponencia['comentario'])) ?></blockquote>
                  </div>
                <?php endif; ?>
                <hr>
            </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</section>
</main>

<?php include 'footer.php'; ?>
</body>
</html>
