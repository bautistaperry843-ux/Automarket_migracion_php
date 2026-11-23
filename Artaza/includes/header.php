<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/db.php';

function pagina_activa($nombre_archivo) {
    $pagina_actual = basename($_SERVER['PHP_SELF']);
    return ($pagina_actual === $nombre_archivo) ? 'nav-active' : '';
}

// Obtener el auto con más favoritos para la "Oferta Destacada"[cite: 19]
$sql_destacado = "SELECT vehiculo_id, COUNT(*) as total_favs FROM favoritos GROUP BY vehiculo_id ORDER BY total_favs DESC LIMIT 1";
$res_destacado = $conn->query($sql_destacado);
$url_destacada = "catalogo.php";
if ($res_destacado && $res_destacado->num_rows > 0) {
    $row_dest = $res_destacado->fetch_assoc();
    $url_destacada = "vehiculo.php?id=" . $row_dest['vehiculo_id'];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($titulo_pagina) ? $titulo_pagina : $app_name; ?></title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>

    <header>
        <div>
            <h1 class="golden-text"><?php echo isset($titulo_pagina) ? $titulo_pagina : $app_name; ?></h1>
            <small class="golden-text" onclick="navigator.clipboard.writeText('Av. Bicentenario 1200'); alert('¡Dirección copiada!');">
                📍 Sucursal: Av. Bicentenario 1200 (Haz click para copiar)
            </small>
        </div>

        <div class="header-right">
            <div class="date-card golden-text">
                📅 <span id="fecha-hora"></span>
            </div>

            <div class="auth-actions">
                <?php if (isset($_SESSION['usuario_id'])): ?>
                    <span class="welcome-pill">Hola, <strong><?= htmlspecialchars($_SESSION['usuario_nombre']) ?></strong></span>
                    <?php if (($_SESSION['rol'] ?? '') === 'admin'): ?>
                        <a href="admin/index.php" class="auth-btn primary">Panel admin</a>
                    <?php endif; ?>
                    <a href="logout.php" class="auth-btn secondary">Cerrar sesión</a>
                <?php else: ?>
                    <a href="inicio de sesion.php" class="auth-btn primary">Iniciar sesión</a>
                    <a href="registro.php" class="auth-btn secondary">Registrarse</a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <nav>
        <details class="nav-dropdown">
            <summary class="golden-text">Catálogo de Vehículos ▾</summary>
            <div class="dropdown-content">
                <a href="catalogo.php" class="<?= pagina_activa('catalogo.php'); ?>">Ver Todos los Autos</a>
                <a href="<?= $url_destacada ?>" class="<?= (basename($_SERVER['PHP_SELF']) == 'vehiculo.php') ? 'nav-active' : ''; ?>">Ver Oferta Destacada</a>
            </div>
        </details>
        <a href="index.php" class="golden-text <?= pagina_activa('index.php'); ?>">Ir al Inicio</a>
        <a href="vender auto.php" class="golden-text <?= pagina_activa('vender auto.php'); ?>">Vender mi Auto</a>
        <a href="curso.php" class="golden-text <?= pagina_activa('curso.php'); ?>">Curso Mecánica</a>
        <a href="favoritos.php" class="golden-text <?= pagina_activa('favoritos.php'); ?>">Favoritos</a>
        <a href="contacto.php" class="golden-text <?= pagina_activa('contacto.php'); ?>">Contacto</a>
        <a href="sobre nosotros.php" class="golden-text <?= pagina_activa('sobre nosotros.php'); ?>">Acerca de Nosotros</a>
        <a href="preguntas frecuentes.php" class="golden-text <?= pagina_activa('preguntas frecuentes.php'); ?>">Preguntas Frecuentes</a>
        <button id="btn-modo-oscuro" onclick="alternarModo()" class="btn-modo-oscuro">
            🌙 Modo Oscuro
        </button>
    </nav>