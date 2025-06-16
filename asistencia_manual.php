<?php
session_start();
require 'db.php';

if (!in_array('admin', $_SESSION['roles'] ?? [])) {
    header('Location: index.php');
    exit;
}

$usuario = null;
$mensaje = null;
$tipo_mensaje = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['buscar'])) {
    $busqueda = trim($_POST['busqueda']);
    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE email = ? OR dni = ? OR nombre LIKE ? OR apellido LIKE ?");
    $like = "%$busqueda%";
    $stmt->bind_param("ssss", $busqueda, $busqueda, $like, $like);
    $stmt->execute();
    $usuario = $stmt->get_result()->fetch_assoc();
}

if (isset($_POST['confirmar']) && isset($_POST['id_usuario'])) {
    $id = $_POST['id_usuario'];
    $hoy = date('Y-m-d');

    // VALIDACIÓN DE FECHA DEL CONGRESO DESACTIVADA TEMPORALMENTE PARA PRUEBAS
    // $inicio_congreso = '2025-06-10';
    // $fin_congreso = '2025-06-12';

    // if ($hoy < $inicio_congreso || $hoy > $fin_congreso) {
    //     $mensaje = "La asistencia solo puede registrarse durante los días del congreso (10 al 12 de junio).";
    //     $tipo_mensaje = "warning";
    // } else {
        $check = $conn->prepare("SELECT * FROM asistencias WHERE id_usuario = ? AND id_evento = 1 AND DATE(fecha_hora) = ?");
        $check->bind_param("is", $id, $hoy);
        $check->execute();
        $res = $check->get_result();

        if ($res->num_rows === 0) {
            $insert = $conn->prepare("INSERT INTO asistencias (id_usuario, id_evento, fecha_hora) VALUES (?, 1, NOW())");
            $insert->bind_param("i", $id);
            $insert->execute();
            $mensaje = "Asistencia registrada correctamente para el día $hoy.";
            $tipo_mensaje = "success";
        } else {
            $mensaje = "Este usuario ya tiene asistencia registrada para el día de hoy.";
            $tipo_mensaje = "warning";
        }
    // }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Registrar Asistencia</title>
  <link rel="stylesheet" href="styles.css" />
</head>
<body>

<?php include 'header.php'; ?>

<main class="admin-container">
  <h2>📝 Registrar Asistencia Manual</h2>

  <form method="post" style="margin-bottom: 2rem;">
    <label for="busqueda">Buscar por DNI, Email o Nombre:</label>
    <input type="text" name="busqueda" id="busqueda" required style="width: 100%; margin: 0.5rem 0;">
    <button type="submit" name="buscar" class="btn">Buscar</button>
  </form>

  <?php if ($usuario): ?>
    <div class="card-usuario">
      <h4>👤 Usuario encontrado</h4>
      <p><strong><?= $usuario['nombre'] . ' ' . $usuario['apellido'] ?></strong> – <?= $usuario['email'] ?></p>
      <p>DNI: <?= $usuario['dni'] ?> | Teléfono: <?= $usuario['telefono'] ?></p>

      <form method="post" style="margin-top: 1rem;">
        <input type="hidden" name="id_usuario" value="<?= $usuario['id'] ?>">
        <button type="submit" name="confirmar" class="btn">Confirmar Asistencia</button>
        <?php if (isset($_POST['confirmar']) && isset($mensaje)): ?>
          <div class="alert alert-<?= $tipo_mensaje ?>" style="margin-top: 1rem;">
            <?= $mensaje ?>
          </div>
        <?php endif; ?>
      </form>
    </div>
  <?php elseif (isset($mensaje)): ?>
    <div class="alert alert-<?= $tipo_mensaje ?>">
      <?= $mensaje ?>
    </div>
  <?php endif; ?>
</main>

<?php include 'footer.php'; ?>

</body>
</html>