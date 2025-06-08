<?php
session_start();
require 'db.php';

if (!isset($_SESSION['roles']) || !in_array('admin', $_SESSION['roles'])) {
    header("Location: index.php");
    exit();
}

// Obtener usuarios y sus roles actuales
$sql = "
SELECT u.id, u.nombre, u.apellido, u.dni, u.email, u.telefono,
       GROUP_CONCAT(r.nombre ORDER BY r.nombre SEPARATOR ', ') AS roles
FROM usuarios u
LEFT JOIN usuario_roles ur ON u.id = ur.id_usuario
LEFT JOIN roles r ON ur.id_rol = r.id
GROUP BY u.id
";
$result = $conn->query($sql);
if (!$result) {
    die("Error en la consulta: " . $conn->error);
}

$total_registros = $result->num_rows;
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Gestión de Usuarios</title>
  <link rel="stylesheet" href="styles.css">
  <script src="js.js"></script>
</head>
<body>
<?php include 'header.php'; ?>
<main>
<section>
  <div class="container">
    <h2>👥 Gestión de Usuarios</h2>
    <p>Total registros encontrados: <?php echo $total_registros; ?></p>
    <table class="tabla-usuarios">
      <tr>
        <th>Nombre</th>
        <th>Apellido</th>
        <th>DNI</th>
        <th>Email</th>
        <th>Teléfono</th>
        <th>Roles actuales</th>
        <th>Agregar rol</th>
      </tr>
      <?php while ($row = $result->fetch_assoc()): ?>
      <tr>
        <td><?= htmlspecialchars($row['nombre']) ?></td>
        <td><?= htmlspecialchars($row['apellido']) ?></td>
        <td><?= htmlspecialchars($row['dni']) ?></td>
        <td><?= htmlspecialchars($row['email']) ?></td>
        <td><?= htmlspecialchars($row['telefono']) ?></td>
        <td><?= htmlspecialchars($row['roles']) ?: 'Ninguno' ?></td>
        <td>
          <form method="POST" action="cambiar_rol.php">
            <input type="hidden" name="email" value="<?= htmlspecialchars($row['email']) ?>">
            <select name="nuevo_rol" required>
              <option value="" disabled selected>Elegir rol</option>
              <option value="user">Usuario</option>
              <option value="ponente">Ponente</option>
              <option value="admin">Admin</option>
            </select>
            <button type="submit">Agregar</button>
          </form>
        </td>
      </tr>
      <?php endwhile; ?>
      <?php if ($total_registros == 0): ?>
      <tr><td colspan="7">No se encontraron registros.</td></tr>
      <?php endif; ?>
    </table>
  </div>
</section>
</main>
<?php include 'footer.php'; ?>
</body>
</html>
