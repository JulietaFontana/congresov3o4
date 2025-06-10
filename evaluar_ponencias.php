<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['roles']) || !in_array('evaluador', $_SESSION['roles'])) {
    die("⛔ Acceso denegado. Esta sección es solo para evaluadores.");
}

$id_evaluador = $_SESSION['id'];

$sql = "
    SELECT p.id, p.archivo, p.fecha_subida, e.nombre AS eje, u.nombre AS autor_nombre, u.apellido AS autor_apellido, pe.evaluacion
    FROM ponencias p
    INNER JOIN ponencia_evaluador pe ON p.id = pe.id_ponencia
    INNER JOIN usuarios u ON p.id_usuario = u.id
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

    <?php while ($row = $resultado->fetch_assoc()): ?>
        <div class="usuario-card">
            <div class="usuario-info">
                <strong>Archivo:</strong> <?= htmlspecialchars($row['archivo']) ?><br>
                <strong>Autor:</strong> <?= htmlspecialchars($row['autor_nombre'] . " " . $row['autor_apellido']) ?><br>
                <strong>Eje temático:</strong> <?= htmlspecialchars($row['eje']) ?><br>
                <strong>Fecha:</strong> <?= $row['fecha_subida'] ?><br>
                <a class="btn" href="ponencias/evaluacion_<?= urlencode($row['archivo']) ?>" target="_blank">📄 Ver archivo</a>
            </div>
            <div class="acciones">
                <?php if ($row['evaluacion']): ?>
                    <p><strong>✅ Evaluado:</strong><br><?= nl2br(htmlspecialchars($row['evaluacion'])) ?></p>
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
