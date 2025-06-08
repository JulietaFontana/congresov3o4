<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: index.php");
    exit();
}

require 'db.php';

$result = $conn->query("SELECT nombre, apellido, dni, email, telefono, rol FROM usuarios");
if (!$result) {
    die("Error en la consulta: " . $conn->error);
}

// Debug: Mostrar número de registros encontrados
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
    <p>Total registros encontrados: <?php echo $total_registros; ?></p> <!-- Línea de debug -->
    <table class="tabla-usuarios">
      <tr>
        <th>Nombre</th>
        <th>Apellido</th>
        <th>DNI</th>
        <th>Email</th>
        <th>Teléfono</th>
        <th>Rol Actual</th>
        <th>Cambiar Rol</th>
      </tr>
      <?php
      if ($total_registros > 0):
          while($row = $result->fetch_assoc()):
      ?>
      <tr>
        <td><?php echo htmlspecialchars($row['nombre']); ?></td>
        <td><?php echo htmlspecialchars($row['apellido']); ?></td>
        <td><?php echo htmlspecialchars($row['dni']); ?></td>
        <td><?php echo htmlspecialchars($row['email']); ?></td>
        <td><?php echo htmlspecialchars($row['telefono']); ?></td>
        <td><?php echo $row['rol']; ?></td>
        <td>
            <form method="POST" action="cambiar_rol.php">
            <input type="hidden" name="email" value="<?php echo $row['email']; ?>">
            <select name="nuevo_rol" required>
                <option value="user" <?php if($row['rol']=='user') echo 'selected'; ?>>Usuario</option>
                <option value="ponente" <?php if($row['rol']=='ponente') echo 'selected'; ?>>Ponente</option>
                <option value="admin" <?php if($row['rol']=='admin') echo 'selected'; ?>>Admin</option>
            </select>
            <button type="submit">Actualizar</button>
            </form>
        </td>
      </tr>
      <?php
          endwhile;
      else:
      ?>
      <tr><td colspan="7">No se encontraron registros.</td></tr>
      <?php endif; ?>
    </table>
  </div>
</section>
</main>
<?php include 'footer.php'; ?>
</body>
</html>
