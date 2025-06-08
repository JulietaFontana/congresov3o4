<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
require 'db.php';

// Mensaje por mostrar (texto + tipo para CSS)
$mensaje = "";
$tipo = ""; // puede ser: success, error, info

if (!isset($_SESSION['email'])) {
    $mensaje = "⚠️ Tenés que iniciar sesión para registrar tu asistencia.";
    $tipo = "error";
} elseif (!isset($_GET['token'])) {
    $mensaje = "❌ Token no proporcionado.";
    $tipo = "error";
} else {
    $token = $_GET['token'];
    $email = $_SESSION['email'];

    // 1. Obtener ID del usuario
    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();
    $user = $res->fetch_assoc();

    if (!$user) {
        $mensaje = "❌ Usuario no encontrado.";
        $tipo = "error";
    } else {
        $id_usuario = $user['id'];

        // 2. Verificar token válido
        $verif = $conn->prepare("SELECT id FROM qr_tokens WHERE token = ? AND valido = 1");
        $verif->bind_param("s", $token);
        $verif->execute();
        $res_verif = $verif->get_result();

        if ($res_verif->num_rows === 0) {
            $mensaje = "❌ Este código ya fue utilizado o es inválido.";
            $tipo = "error";
        } else {
            // 3. Verificar duplicado
            $check = $conn->prepare("SELECT COUNT(*) AS total FROM asistencias WHERE id_usuario = ? AND token = ?");
            $check->bind_param("is", $id_usuario, $token);
            $check->execute();
            $check_result = $check->get_result()->fetch_assoc();

            if ($check_result['total'] > 0) {
                $mensaje = "🔁 Ya registraste tu asistencia con este código.";
                $tipo = "info";
            } else {
                // 4. Registrar asistencia
                try {
                    $insert = $conn->prepare("INSERT INTO asistencias (id_usuario, token) VALUES (?, ?)");
                    $insert->bind_param("is", $id_usuario, $token);
                    $insert->execute();

                    // 5. Invalidar token
                    $update = $conn->prepare("UPDATE qr_tokens SET valido = 0 WHERE token = ?");
                    $update->bind_param("s", $token);
                    $update->execute();

                    $mensaje = "✅ ¡Gracias! Tu asistencia fue registrada correctamente.";
                    $tipo = "success";
                } catch (mysqli_sql_exception $e) {
                    $mensaje = "❌ No se pudo registrar la asistencia.";
                    $tipo = "error";
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Registro de Asistencia</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>

<?php include 'header.php'; ?>

<main>
  <div class="asistencia-card">
    <?php if ($mensaje): ?>
      <p class="<?= htmlspecialchars($tipo) ?>"><?= htmlspecialchars($mensaje) ?></p>
    <?php endif; ?>
  </div>
</main>

<?php include 'footer.php'; ?>

</body>
</html>
