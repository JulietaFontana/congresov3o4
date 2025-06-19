<?php
session_start();
require 'db.php';

if (!in_array('admin', $_SESSION['roles'] ?? [])) {
    header('Location: index.php');
    exit;
}

// Usuarios ordenados por apellido
$usuarios = $conn->query("SELECT id, apellido, nombre FROM usuarios ORDER BY apellido, nombre")->fetch_all(MYSQLI_ASSOC);

// Días del congreso
$fechas = ['2025-06-10', '2025-06-11', '2025-06-12'];

// Obtener asistencias ya registradas
$asistencias = [];
$sql = "SELECT id_usuario, DATE(fecha_hora) as fecha FROM asistencias WHERE id_evento = 1";
$res = $conn->query($sql);
while ($row = $res->fetch_assoc()) {
    $asistencias[$row['id_usuario']][$row['fecha']] = true;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Asistencia Manual por Día</title>
  <link rel="stylesheet" href="styles.css">
  <style>
    table { border-collapse: collapse; width: 100%; }
    th, td { border: 1px solid #ccc; padding: 6px; text-align: center; }
    th { background-color: #f0f0f0; }
    input[type="checkbox"]:checked + label::after {
      content: '✔';
      color: green;
      margin-left: 4px;
    }
  </style>
</head>
<body>

<?php include 'header.php'; ?>

<main class="admin-container">
  <h2>📋 Asistencia Manual por Día</h2>

  <?php if (isset($_GET['guardado'])): ?>
    <div class="alert alert-success">✅ Asistencias guardadas correctamente.</div>
  <?php endif; ?>

  <form method="post" action="guardar_asistencias_dias.php">
    <table>
      <thead>
        <tr>
          <th>Apellido, Nombre</th>
          <?php foreach ($fechas as $fecha): ?>
            <th><?= date('d/m/Y', strtotime($fecha)) ?></th>
          <?php endforeach; ?>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($usuarios as $u): ?>
          <tr>
            <td><?= htmlspecialchars($u['apellido']) ?>, <?= htmlspecialchars($u['nombre']) ?></td>
            <?php foreach ($fechas as $fecha): ?>
              <?php $checked = isset($asistencias[$u['id']][$fecha]) ? 'checked' : ''; ?>
              <td>
                <input type="checkbox" name="asistencias[<?= $u['id'] ?>][<?= $fecha ?>]" value="1" <?= $checked ?>>
              </td>
            <?php endforeach; ?>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <button type="submit" class="btn">💾 Guardar Cambios</button>
  </form>
</main>

<?php include 'footer.php'; ?>

</body>
</html>
