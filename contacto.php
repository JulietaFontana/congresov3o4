<?php session_start(); ?>
<?php include 'header.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Contacto</title>
  <link rel="stylesheet" href="styles.css" />
  <script src="js.js" defer></script>
  <script src="js.js"></script>
</head>
<body>
  
  <main>
    <section id="contacto">
    <div class="container">
        <h2>Contacto</h2>
        <p>¿Tienes alguna pregunta sobre el congreso? No dudes en contactarnos.</p>
        
        <div class="card-grid">
            <div class="card">
                <div class="card-img">📧</div>
                <div class="card-content">
                    <h3>Email</h3>
                    <p>info@congresotecnologico.org</p>
                </div>
            </div>
            <div class="card">
                <div class="card-img">📞</div>
                <div class="card-content">
                    <h3>Teléfono</h3>
                    <p>+34 900 123 456</p>
                </div>
            </div>
            <div class="card">
                <div class="card-img">📍</div>
                <div class="card-content">
                    <h3>Ubicación</h3>
                    <p>Centro de Convenciones Tecnológicas<br>Av. de la Innovación, 123<br>Madrid, España</p>
                </div>
            </div>
        </div>
    </div>
</section>
<?php include 'footer.php'; ?>
  </main>
</body>
</html>