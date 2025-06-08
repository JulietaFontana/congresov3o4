<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['roles']) || !in_array('admin', $_SESSION['roles'])) {
    header("Location: login.php");
    exit();
}

$sql = "SELECT u.id, u.nombre, u.apellido, u.email, u.dni, u.telefono,
               GROUP_CONCAT(r.nombre) AS roles
        FROM usuarios u
        LEFT JOIN usuario_roles ur ON u.id = ur.id_usuario
        LEFT JOIN roles r ON ur.id_rol = r.id
        GROUP BY u.id";
$result = $conn->query($sql);

$roles_result = $conn->query("SELECT id, nombre FROM roles WHERE nombre != 'user'");
$roles_disponibles = [];
while ($r = $roles_result->fetch_assoc()) {
    $roles_disponibles[$r['id']] = $r['nombre'];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Usuarios</title>
    <link rel="stylesheet" href="styles.css">
    <script src="js.js"></script>
    <script>
      function toggleNuevoUsuario() {
        const form = document.getElementById('nuevoForm');
        form.style.display = (form.style.display === 'block') ? 'none' : 'block';
      }
    </script>
</head>
<body>
<?php include 'header.php'; ?>
<main>
    <div class="container">
        <h2>👥 Gestión de Usuarios</h2>

        <form method="POST" action="guardar_roles.php">
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="usuario-card">
                <div class="usuario-info">
                    <input type="hidden" name="usuarios[]" value="<?= $row['id'] ?>">
                    <p><strong><?= htmlspecialchars($row['nombre']) . ' ' . htmlspecialchars($row['apellido']) ?></strong> (<?= $row['email'] ?>)</p>
                    <p>DNI: <?= $row['dni'] ?> | Teléfono: <?= $row['telefono'] ?></p>
                    <?php foreach ($roles_disponibles as $id_rol => $nombre_rol): ?>
                        <label style="margin-right: 10px">
                            <input type="checkbox" name="roles[<?= $row['id'] ?>][]" value="<?= $id_rol ?>"
                                <?= in_array($nombre_rol, explode(',', $row['roles'])) ? 'checked' : '' ?>>
                            <?= ucfirst($nombre_rol) ?>
                        </label>
                    <?php endforeach; ?>
                </div>
                <div class="acciones">
                    <a href="eliminar_usuario.php?id=<?= $row['id'] ?>" onclick="return confirm('¿Eliminar este usuario?')">
                        🗑️
                    </a>
                </div>
            </div>
        <?php endwhile; ?>

        <div class="botones-finales">
            <button type="button" onclick="toggleNuevoUsuario()" class="btn-principal">➕ Dar de alta nuevo usuario</button>
            <button type="submit" class="btn-principal">Guardar</button>
        </div>
        </form>

        <form method="POST" action="alta_usuario.php" class="nuevo-usuario-form" id="nuevoForm">
            <h3>Nuevo Usuario</h3>
            <input type="text" name="nombre" placeholder="Nombre" required>
            <input type="text" name="apellido" placeholder="Apellido" required>
            <input type="text" name="dni" placeholder="DNI" required>
            <input type="text" name="telefono" placeholder="Teléfono" required>
            <input type="email" name="email" placeholder="Correo electrónico" required>
            <input type="text" name="username" placeholder="Usuario" required>
            <input type="password" name="password" placeholder="Contraseña" required>
            <p>Roles:</p>
            <?php foreach ($roles_disponibles as $id_rol => $nombre_rol): ?>
                <label style="margin-right: 10px">
                    <input type="checkbox" name="roles[]" value="<?= $id_rol ?>">
                    <?= ucfirst($nombre_rol) ?>
                </label>
            <?php endforeach; ?>
            <br><br>
            <button type="submit" class="btn-principal">Registrar nuevo usuario</button>
        </form>
    </div>
</main>
<?php include 'footer.php'; ?>
</body>
</html>
