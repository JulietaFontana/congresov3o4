<?php
session_start();
?>

<header>
  <div class="top-bar2">
    <div class="header-brand">
      <img src="img/redgtec.png" alt="Logo Congreso" class="logo-congreso">
      <span class="nombre-congreso">Congreso de Gestores Tecnológicos</span>
    </div>

    <div class="login-container">
      <?php if (!isset($_SESSION['nombre'])): ?>
        <a href="login.php" class="login-link">Iniciar sesión</a>
      <?php else: ?>
        <span>👋 <?= $_SESSION['nombre'] . ' ' . $_SESSION['apellido']; ?></span>
        <a href="logout.php" class="logout-link">Cerrar sesión</a>

        <!-- 🔔 Notificaciones -->
        <?php
        require_once 'db.php';
        $email = $_SESSION['email'];
        $stmt = $conn->prepare("SELECT mensaje, leida FROM notificaciones WHERE usuario_email = ? ORDER BY fecha DESC LIMIT 5");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $notif_result = $stmt->get_result();
        $notif_count = 0;
        $notif_list = [];
        while ($row = $notif_result->fetch_assoc()) {
            $notif_list[] = $row;
            if (!$row['leida']) $notif_count++;
        }
        ?>
        <div class="notif-container">
          <div class="notif-icon" onclick="toggleNotif()">
            🔔 <span id="notif-count" <?= $notif_count > 0 ? 'class="active"' : '' ?>>
              <?= $notif_count ?>
            </span>
          </div>
          <div id="notif-dropdown" class="notif-dropdown hidden">
            <?php if (empty($notif_list)): ?>
              <p>No hay notificaciones.</p>
            <?php else: ?>
              <?php foreach ($notif_list as $notif): ?>
                <p><?= htmlspecialchars($notif['mensaje']); ?><?= !$notif['leida'] ? " 🔴" : "" ?></p>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <nav>
    <a href="index.php">Inicio</a>
    <a href="acerca.php">Acerca</a>
    <a href="Ponentes.php">Expositores</a>
    <a href="agenda.php">Agenda</a>
    <a href="contacto.php">Contacto</a>
    <a href="certificaciones.php">Certificados</a>

    <?php if (isset($_SESSION['roles']) && in_array('ponente', $_SESSION['roles'])): ?>
      <a href="subir_ponencia.php" style="color: #ffcc00;">📄 Subir ponencia</a>
    <?php endif; ?>
    <?php if (isset($_SESSION['roles']) && in_array('evaluador', $_SESSION['roles'])): ?>
      <a href="evaluar_ponencias.php" style="color: #ffcc00;">📑 Evaluar Ponencias</a>
    <?php endif; ?>
    <?php if (isset($_SESSION['roles']) && in_array('admin', $_SESSION['roles'])): ?>
      <div class="admin-dropdown">
        <button onclick="toggleAdminMenu()">📂 Panel Administrador</button>
        <div id="admin-menu" class="dropdown-content hidden">
          <a href="gestionar_usuarios.php">👥 Gestionar Usuarios</a>
          <a href="gestionar_ejes.php">📚 Gestionar Ejes</a>
          <?php
            $hoy = date('Y-m-d');
            $inicio_congreso = '2020-06-10';
            $fin_congreso = '2029-06-12';
            if ($hoy >= $inicio_congreso && $hoy <= $fin_congreso):
          ?>
            <a href="qr_dinamico.php">📷 QR Asistencia</a>
          <?php endif; ?>
        </div>
      </div>
    <?php endif; ?>
  </nav>
</header>
