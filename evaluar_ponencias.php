<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['roles']) || !in_array('evaluador', $_SESSION['roles'])) {
    die("⛔ Acceso denegado. Esta sección es solo para evaluadores.");
}

$id_evaluador = $_SESSION['id'];

$sql = "
    SELECT p.id, p.archivo, p.fecha_subida, e.nombre AS eje,
           p.resumen, p.palabras_clave, pe.evaluacion, pe.estado
    FROM ponencias p
    INNER JOIN ponencia_evaluador pe ON p.id = pe.id_ponencia
    INNER JOIN ejes e ON p.id_eje = e.id
    WHERE pe.id_evaluador = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_evaluador);
$stmt->execute();
$resultado = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Evaluar Ponencias</title>
    <link rel="stylesheet" href="styles.css">
    <script src="js.js"></script>
</head>
<body>
<?php include 'header.php'; ?>
<main class="container">
    <h2>Ponencias Asignadas</h2>

    <?php while ($row = $resultado->fetch_assoc()):
        $archivo_anonimo = str_replace("ponencia_", "", $row['archivo']);
    ?>
        <div class="usuario-card">
            <div class="usuario-info">
                <strong>Archivo:</strong> 
                <a href="ponencias/evaluacion_<?= urlencode($archivo_anonimo) ?>" target="_blank">📄 Ver archivo</a><br>
                <strong>Eje temático:</strong> <?= htmlspecialchars($row['eje']) ?><br>
                <strong>Fecha de envío:</strong> <?= $row['fecha_subida'] ?><br>
                <strong>Palabras clave:</strong> <?= htmlspecialchars($row['palabras_clave']) ?><br>
                <strong>Resumen:</strong><br>
                <div style="margin-left: 1em;"><?= nl2br(htmlspecialchars($row['resumen'])) ?></div>
            </div>
            <div class="acciones">
                <?php if ($row['evaluacion']): ?>
                    <p><strong>✅ Evaluado:</strong><br><?= nl2br(htmlspecialchars($row['evaluacion'])) ?></p>
                    <p><strong>Estado:</strong> <?= $row['estado'] === 'aprobada' ? '✅ Aprobada' : '❌ Desaprobada' ?></p>
                <?php else: ?>
                    <form method="POST" action="guardar_evaluacion.php">
                        <input type="hidden" name="id_ponencia" value="<?= $row['id'] ?>">
                        <textarea name="evaluacion" rows="4" cols="30" placeholder="Escribí tu evaluación..." required></textarea>
                        <br>
                        <label>
                            <input type="radio" name="estado" value="aprobada" required> ✅ Aprobar
                        </label>
                        <label>
                            <input type="radio" name="estado" value="desaprobada"> ❌ Desaprobar
                        </label>
                        <br>
                        <button class="btn-principal" type="submit">Enviar evaluación</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    <?php endwhile; ?>
</main>
<?php include 'footer.php'; ?>
</body>
</html>
