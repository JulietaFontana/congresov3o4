<?php session_start(); ?>
<?php include 'header.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Agenda</title>
  <link rel="stylesheet" href="styles.css" />
  <script src="js.js" defer></script>
</head>
<body>
  
  <main>
    <section id="agenda">
    <div class="container">
        <h2>Agenda del Congreso</h2>
        
        <h3>Día 1: Innovación y Tendencias</h3>
        <div class="agenda-item">
            <p class="agenda-time">09:00 - 10:00</p>
            <h4>Registro y acreditación</h4>
        </div>
        <div class="agenda-item">
            <p class="agenda-time">10:00 - 11:30</p>
            <h4>Conferencia inaugural: "El futuro de la gestión tecnológica"</h4>
            <p>Dr. Carlos Méndez</p>
        </div>
        <div class="agenda-item">
            <p class="agenda-time">11:45 - 13:00</p>
            <h4>Panel: "Tendencias tecnológicas que transformarán los negocios"</h4>
        </div>
        <div class="agenda-item">
            <p class="agenda-time">14:30 - 16:00</p>
            <h4>Talleres paralelos: IA, Blockchain, Cloud Computing</h4>
        </div>
        
        <h3>Día 2: Implementación y Casos de Éxito</h3>
        <div class="agenda-item">
            <p class="agenda-time">09:30 - 11:00</p>
            <h4>Conferencia: "Transformación digital en la industria"</h4>
            <p>Ing. Laura Sánchez</p>
        </div>
        <div class="agenda-item">
            <p class="agenda-time">11:15 - 12:30</p>
            <h4>Casos de éxito: Implementaciones tecnológicas exitosas</h4>
        </div>
        <div class="agenda-item">
            <p class="agenda-time">14:00 - 16:30</p>
            <h4>Workshops: Gestión de proyectos tecnológicos</h4>
        </div>
        
        <h3>Día 3: Futuro y Networking</h3>
        <div class="agenda-item">
            <p class="agenda-time">09:30 - 11:00</p>
            <h4>Conferencia: "Ciudades inteligentes y sostenibilidad"</h4>
            <p>Dra. Ana María Rodríguez</p>
        </div>
        <div class="agenda-item">
            <p class="agenda-time">11:15 - 13:00</p>
            <h4>Mesa redonda: "El futuro del trabajo en la era digital"</h4>
        </div>
        <div class="agenda-item">
            <p class="agenda-time">14:30 - 16:00</p>
            <h4>Ceremonia de clausura y networking</h4>
        </div>
    </div>
</section>
<?php include 'footer.php'; ?>
  </main>
</body>
</html>