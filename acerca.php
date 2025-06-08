<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Acerca</title>
  <link rel="stylesheet" href="styles.css" />
  <script src="js.js" defer></script>
</head>
<body>
    <?php include 'header.php'; ?>
  
  <main>
    <section id="acerca">
    <div class="container">
        <h2>Acerca del Congreso</h2>
        <p>El Congreso Nacional de Gestores Tecnológicos es el evento más importante del año para profesionales dedicados a la gestión de tecnología e innovación. Durante tres días intensivos, exploraremos los desafíos y oportunidades que presenta la transformación digital en diversos sectores.</p>
        
        <div class="card-grid">
            <div class="card">
                <div class="card-img">📊</div>
                <div class="card-content">
                    <h3>Networking</h3>
                    <p>Conecta con más de 500 profesionales del sector tecnológico, ampliando tu red de contactos profesionales.</p>
                </div>
            </div>
            <div class="card">
                <div class="card-img">💡</div>
                <div class="card-content">
                    <h3>Conferencias</h3>
                    <p>Más de 30 conferencias con expertos nacionales e internacionales sobre las últimas tendencias tecnológicas.</p>
                </div>
            </div>
            <div class="card">
                <div class="card-img">🔬</div>
                <div class="card-content">
                    <h3>Talleres</h3>
                    <p>Talleres prácticos para desarrollar habilidades específicas en gestión de proyectos tecnológicos.</p>
                </div>
            </div>
            <div class="card">
                <div class="card-img">📈</div>
                <div class="card-content">
                    <h3>Panel de Administración y Estadísticas</h3>
                    <p>Acceso exclusivo para organizadores y patrocinadores a datos en tiempo real sobre asistencia, participación y evaluaciones del evento.</p>
                </div>
            </div>
        </div>
        
        <!-- Nuevo apartado: Panel de administración y estadísticas -->
        <div class="admin-panel">
            <h3>Panel de Administración y Estadísticas</h3>
            <p>Nuestro congreso cuenta con un avanzado sistema de gestión que permite monitorear en tiempo real todos los aspectos del evento. Los organizadores y patrocinadores tienen acceso a un completo panel de control con métricas y estadísticas actualizadas.</p>
            
            <div class="stats-container">
                <div class="stat-box">
                    <div class="stat-number">487</div>
                    <div class="stat-label">Inscritos confirmados</div>
                </div>
                <div class="stat-box">
                    <div class="stat-number">32</div>
                    <div class="stat-label">Ponentes</div>
                </div>
                <div class="stat-box">
                    <div class="stat-number">18</div>
                    <div class="stat-label">Patrocinadores</div>
                </div>
                <div class="stat-box">
                    <div class="stat-number">94%</div>
                    <div class="stat-label">Satisfacción edición anterior</div>
                </div>
            </div>
            
            <h4 style="margin-top: 30px; color: #004080;">Distribución de asistentes por sector</h4>
            <div class="chart-container">
                <div class="chart-bar" style="height: 65%; --final-height: 65%;" data-value="32%">
                    <div class="chart-label">Tecnología</div>
                </div>
                <div class="chart-bar" style="height: 45%; --final-height: 45%;" data-value="23%">
                    <div class="chart-label">Educación</div>
                </div>
                <div class="chart-bar" style="height: 35%; --final-height: 35%;" data-value="18%">
                    <div class="chart-label">Finanzas</div>
                </div>
                <div class="chart-bar" style="height: 25%; --final-height: 25%;" data-value="12%">
                    <div class="chart-label">Salud</div>
                </div>
                <div class="chart-bar" style="height: 30%; --final-height: 30%;" data-value="15%">
                    <div class="chart-label">Otros</div>
                </div>
            </div>
            
            <p>El panel de administración permite:</p>
            <ul style="margin: 15px 0 20px 20px;">
                <li>Seguimiento en tiempo real de registros y asistencia</li>
                <li>Gestión de ponentes, salas y horarios</li>
                <li>Análisis de participación en cada actividad</li>
                <li>Recopilación de feedback y evaluaciones</li>
                <li>Generación de informes personalizados</li>
            </ul>
            
            <a href="#" class="btn">Solicitar acceso al panel</a>
        </div>
    </div>
<?php include 'footer.php'; ?>
  </main>
</body>
</html>