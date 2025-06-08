<?php
session_start();
require 'db.php';

// Verificar que el usuario esté logueado usando el email
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

// Obtener el email del usuario actual
$email_usuario = $_SESSION['email'];

// Consultar la base para obtener el ID del usuario
$sql_id = "SELECT id FROM usuarios WHERE email = ?";
$stmt_id = $conn->prepare($sql_id);
$stmt_id->bind_param("s", $email_usuario);
$stmt_id->execute();
$result_id = $stmt_id->get_result();
$row_id = $result_id->fetch_assoc();
$id_usuario = $row_id['id'] ?? null;

if (!$id_usuario) {
    echo "No se pudo encontrar el usuario.";
    exit();
}

// Obtener certificados emitidos
$sql = "SELECT nombre_evento, fecha_emision, archivo_certificado FROM certificados WHERE id_usuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$res = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Mis Certificados</title>
  <link rel="stylesheet" href="styles.css">
  <script src="js.js"></script>
</head>
<body>

<?php include 'header.php'; ?>

<main>
  <div class="container">
    <h1>Certificados Emitidos</h1>
    <ul>
      <?php while ($c = $res->fetch_assoc()): ?>
        <li>
          <?php echo $c['nombre_evento']; ?> - <?php echo $c['fecha_emision']; ?>
          [<a href="<?php echo $c['archivo_certificado']; ?>" target="_blank">Ver</a>]
        </li>
      <?php endwhile; ?>
    </ul>

    <h2>Solicitar Nuevo Certificado</h2>
    <form method="post" action="generar_certificado.php">
      <button class="btn" type="submit">Generar Nuevo Certificado</button>
    </form>
  </div>
</main>

<?php include 'footer.php'; ?>

</body>
</html>
