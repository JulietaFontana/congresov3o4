<?php
session_start();
if (!in_array('admin', $_SESSION['roles'] ?? [])) {
    header('Location: index.php');
    exit;
}
require 'db.php';

$success = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mensaje = trim($_POST['mensaje'] ?? '');
    $tipo = $_POST['tipo'] ?? 'general';
    $email = $tipo === 'personal' ? trim($_POST['usuario_email'] ?? '') : null;

    if (!empty($mensaje) && ($tipo === 'general' || ($tipo === 'personal' && !empty($email)))) {
        $sql = "INSERT INTO notificaciones (usuario_email, mensaje, tipo, fecha) VALUES (?, ?, ?, NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $email, $mensaje, $tipo);
        $stmt->execute();
        $success = true;
    }
}

$res = $conn->query("SELECT * FROM notificaciones ORDER BY fecha DESC");
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Gestión de Notificaciones</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <?php include 'header.php'; ?>

  <main>
    <section class="container">
      <h2>Gestión de Notificaciones</h2>

      <?php if ($success): ?>
        <div class="success-message">✅ Notificación agregada correctamente.</div>
      <?php endif; ?>

      <form method="POST" action="">
        <label for="tipo">Tipo de notificación:</label><br>
        <select name="tipo" id="tipo" onchange="toggleEmailInput()">
          <option value="general">General (visible para todos)</option>
          <option value="personal">Personal (dirigida a un usuario)</option>
        </select><br><br>

        <div id="emailField" style="display: none;">
          <label for="usuario_email">Email del destinatario:</label><br>
          <input type="email" name="usuario_email" placeholder="usuario@correo.com"><br><br>
        </div>

        <label for="mensaje">Mensaje:</label><br>
        <textarea name="mensaje" rows="3" required placeholder="Escribí una nueva notificación..." style="width: 100%;"></textarea><br><br>
        <button type="submit" class="btn">📢 Publicar</button>
      </form>

      <h3>Historial de notificaciones</h3>
      <ul>
        <?php while ($fila = $res->fetch_assoc()): ?>
          <li>
            <strong><?= date("d/m/Y H:i", strtotime($fila['fecha'])); ?>:</strong>
            <?= htmlspecialchars($fila['mensaje']); ?>
            (<?= $fila['tipo'] === 'personal' ? '🔒 Personal a ' . htmlspecialchars($fila['usuario_email']) : '🌍 General' ?>)
          </li>
        <?php endwhile; ?>
      </ul>
    </section>
  </main>

  <script>
    function toggleEmailInput() {
      const tipo = document.getElementById("tipo").value;
      const emailField = document.getElementById("emailField");
      emailField.style.display = tipo === "personal" ? "block" : "none";
    }
  </script>

  <?php include 'footer.php'; ?>
</body>
</html>
