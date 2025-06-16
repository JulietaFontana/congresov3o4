<?php session_start(); ?>
<?php include 'header.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Registro</title>
  <link rel="stylesheet" href="styles.css" />
</head>
<body>
  
  <main>
    <section id="registro">
      <div class="container">
        <h2>Registro de Usuario</h2>
        <form method="POST" action="insertar_usuario.php" onsubmit="return validarFormulario()">

          <div class="form-row">
            <div class="form-group">
              <label for="nombre">Nombre</label>
              <input type="text" id="nombre" name="nombre" pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+" title="Solo letras y espacios" required>
            </div>
            <div class="form-group">
              <label for="apellido">Apellido</label>
              <input type="text" id="apellido" name="apellido" pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+" title="Solo letras y espacios" required>
            </div>
          </div>

          <div class="form-row">
  <div class="form-group">
    <label for="tipo_doc">Tipo de documento</label>
    <select id="tipo_doc" name="tipo_doc" required>
      <option value="">Seleccionar</option>
      <option value="DNI">DNI</option>
      <option value="Pasaporte">Pasaporte</option>
      <option value="Otro">Otro</option>
    </select>
  </div>

  <div class="form-group">
    <label for="dni">Número de documento</label>
    <input type="text" id="dni" name="dni" pattern="\d{7,9}" title="Debe tener entre 7 y 9 dígitos numéricos" required>
  </div>
</div>


          <div class="form-row">
            <div class="form-group">
              <label for="cod_area">Código de área</label>
              <input type="text" id="cod_area" name="cod_area" pattern="\d{2,4}" title="Ej: 223" required>
            </div>
            <div class="form-group">
              <label for="telefono">Teléfono (sin 15)</label>
              <input type="text" id="telefono" name="telefono" pattern="\d{6,8}" title="Ej: 12345678" required>
            </div>
          </div>

          <div class="form-group">
            <label for="email">Gmail</label>
            <input type="email" id="email" name="email" pattern="^[a-zA-Z0-9._%+-]+@gmail\.com$" title="Debe ser una dirección @gmail.com" required>
          </div>

          <div class="form-group">
            <label for="password">Contraseña</label>
            <input type="password" id="password" name="password" required>
          </div>

          <div class="form-group">
            <label for="confirmar_password">Confirmar contraseña</label>
            <input type="password" id="confirmar_password" name="confirmar_password" required>
          </div>

          <button type="submit" class="btn">Registrarse</button>
        </form>
      </div>
    </section>
  </main>

  <script>
    function validarFormulario() {
      const password = document.getElementById('password').value;
      const confirmar = document.getElementById('confirmar_password').value;

      if (password !== confirmar) {
        alert("Las contraseñas no coinciden.");
        return false;
      }

      return true;
    }
  </script>
</body>
</html>
