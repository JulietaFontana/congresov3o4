<?php session_start(); ?>
<?php include 'header.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Iniciar sesión</title>
  <link rel="stylesheet" href="styles.css" />
</head>
<body>

<main>
  <section class="login-section">
    <div class="container">
      <h2>Iniciar sesión</h2>

      <?php if (isset($_SESSION['login_error'])): ?>
        <div class="error-message" style="color: red; text-align: center;">
          <?php echo $_SESSION['login_error']; unset($_SESSION['login_error']); ?>
        </div>
      <?php endif; ?>

      <form method="POST" action="validar_login.php">
        <div class="form-group">
            <label for="email">Correo electrónico</label>
            <input type="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="password">Contraseña</label>
            <input type="password" name="password" required>
        </div>
        <button type="submit" class="btn">Entrar</button>
      </form>

      <p style="text-align: center; margin-top: 15px;">
        ¿No tenés cuenta? <a href="registro.php">Registrate aquí</a>
      </p>
      <p style="text-align: center; margin-top: 10px;">
        <a href="index.php">← Volver al inicio</a>
      </p>
    </div>
  </section>
</main>

<?php include 'footer.php'; ?>

</body>
</html>
