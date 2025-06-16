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

// Obtener todas las ponencias del usuario con los nuevos campos
$stmt = $conn->prepare("
    SELECT 
        p.id,
        p.archivo, 
        p.fue_evaluada, 
        e.nombre AS eje_nombre,
        p.universidad,
        p.autores_colaboradores,
        p.palabras_clave,
        p.resumen,
        GROUP_CONCAT(pe.evaluacion SEPARATOR ' | ') AS comentario,
        GROUP_CONCAT(pe.estado SEPARATOR ' | ') AS estado
    FROM ponencias p
    JOIN ejes e ON p.id_eje = e.id
    LEFT JOIN ponencia_evaluador pe ON p.id = pe.id_ponencia
    WHERE p.id_usuario = ?
    GROUP BY p.id
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
            <label for="id_eje">Seleccioná el eje temático:</label>
            <select name="id_eje" required>
                <option value="" disabled selected>-- Seleccioná un eje --</option>
                <?php while ($eje = $ejes_resultado->fetch_assoc()): ?>
                    <option value="<?= $eje['id'] ?>"><?= htmlspecialchars($eje['nombre']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="universidad">Universidad:</label>
            <input type="text" name="universidad" required>
        </div>

        <div class="form-group">
            <label for="autores">Correos de colaboradores (uno por línea):</label>
            <textarea name="autores" rows="4" placeholder="Ej: juan@mail.com" required></textarea>
        </div>

        <div class="form-group">
            <label for="palabras_clave">Palabras clave (separadas por coma):</label>
            <input type="text" name="palabras_clave" placeholder="educación, tecnología, innovación" required>
        </div>

        <div class="form-group">
            <label for="resumen">Resumen:</label>
            <textarea name="resumen" rows="6" required></textarea>
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
                <p><strong>Archivo:</strong> <a href="ponencias/<?= htmlspecialchars($ponencia['archivo']) ?>" target="_blank">Ver</a></p>
                
                <?php if (!empty($ponencia['universidad'])): ?>
                    <p><strong>Universidad:</strong> <?= htmlspecialchars($ponencia['universidad']) ?></p>
                <?php endif; ?>

                <?php if (!empty($ponencia['autores_colaboradores'])): ?>
                    <p><strong>Autores colaboradores:</strong><br><?= nl2br(htmlspecialchars($ponencia['autores_colaboradores'])) ?></p>
                <?php endif; ?>

                <?php if (!empty($ponencia['palabras_clave'])): ?>
                    <p><strong>Palabras clave:</strong> <?= htmlspecialchars($ponencia['palabras_clave']) ?></p>
                <?php endif; ?>

                <?php if (!empty($ponencia['resumen'])): ?>
                    <p><strong>Resumen:</strong><br><?= nl2br(htmlspecialchars($ponencia['resumen'])) ?></p>
                <?php endif; ?>

                <p><strong>Estado de evaluación:</strong> 
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
