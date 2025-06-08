<?php
session_start();
require_once 'db.php';

// ✅ Solo admins pueden acceder
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// ✅ Controlar fechas válidas del congreso
$hoy = date('Y-m-d');
$inicioCongreso = '2020-06-10';
$finCongreso = '2028-06-12';

$accesoHabilitado = ($hoy >= $inicioCongreso && $hoy <= $finCongreso);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Registro de Asistencia - QR Dinámico</title>
  <script src="qrcode.min.js"></script>
  <link rel="stylesheet" href="styles.css">
</head>
<body class="qr-page">

<?php include 'header.php'; ?>

<main>
  <div class="qr-card">
    <h2>📲 Escaneá para registrar asistencia</h2>

    <?php if ($accesoHabilitado): ?>
      <div class="qr-subtitle">Este código se actualiza automáticamente cada 60 segundos</div>
      
      <div id="qrcode"></div>

      <div id="countdown">
        <span class="emoji">⏳</span>
        <span id="count-text">Actualizando en: 60s</span>
      </div>

      <p id="debug"></p>
    <?php else: ?>
      <p class="info">⚠️ El acceso al QR está habilitado solo entre el <?= $inicioCongreso ?> y el <?= $finCongreso ?>.</p>
    <?php endif; ?>
  </div>
</main>

<?php include 'footer.php'; ?>

<?php if ($accesoHabilitado): ?>
<script>
let segundosRestantes = 60;
let countdownInterval;

function iniciarContador() {
  segundosRestantes = 60;
  document.getElementById("count-text").innerText = "Actualizando en: 60s";

  if (countdownInterval) clearInterval(countdownInterval);

  countdownInterval = setInterval(() => {
    segundosRestantes--;
    document.getElementById("count-text").innerText = `Actualizando en: ${segundosRestantes}s`;
    if (segundosRestantes <= 0) clearInterval(countdownInterval);
  }, 1000);
}

async function generarQR() {
  try {
    const res = await fetch("generar_qr.php");
    const token = (await res.text()).trim();

    if (!token || token.length < 10) throw new Error("Token inválido o vacío");

    const url = `http://localhost/congreso/asistir.php?token=${token}`;
    document.getElementById("debug").innerText = token; // Oculto por CSS

    const contenedor = document.getElementById("qrcode");
    contenedor.innerHTML = "";

    const canvas = document.createElement("canvas");

    // ✅ Estilos para centrar el QR
    canvas.style.display = "block";
    canvas.style.margin = "0 auto";
    canvas.style.width = "100%";
    canvas.style.maxWidth = "256px";

    contenedor.appendChild(canvas);

    QRCode.toCanvas(canvas, url, function (error) {
      if (error) {
        console.error("⚠️ Error al generar QR:", error);
        document.getElementById("count-text").innerText = "❌ Error al generar QR";
      }
    });

    iniciarContador();
  } catch (e) {
    console.error("⚠️ Error general:", e);
    document.getElementById("count-text").innerText = "❌ Error: " + e.message;
  }
}

generarQR();
setInterval(generarQR, 60000);
</script>
<?php endif; ?>

</body>
</html>
