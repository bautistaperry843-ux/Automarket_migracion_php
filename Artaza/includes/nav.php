<?php
$pagina_actual = basename($_SERVER['PHP_SELF']);
?>
<nav>
    <details class="nav-dropdown">
        <summary>Catálogo de Vehículos ▾</summary>
        <div class="dropdown-content">
            <a href="catalogo.php" class="<?= $pagina_actual == 'catalogo.php' ? 'active' : ''; ?>">Ver Todos los Autos</a>
            <a href="detalle-de-autos.php" class="<?= $pagina_actual == 'detalle-de-autos.php' ? 'active' : ''; ?>">Ver Oferta Destacada</a>
        </div>
    </details>
    <a href="index.php" class="<?= $pagina_actual == 'index.php' ? 'active' : ''; ?>">Ir al Inicio</a>
    <a href="vender-auto.php" class="<?= $pagina_actual == 'vender-auto.php' ? 'active' : ''; ?>">Vender mi Auto</a>
    <a href="curso.php" class="<?= $pagina_actual == 'curso.php' ? 'active' : ''; ?>">Curso Mecánica</a>
    <a href="blog.php" class="<?= $pagina_actual == 'blog.php' ? 'active' : ''; ?>">Blog</a>
    <a href="contacto.php" class="<?= $pagina_actual == 'contacto.php' ? 'active' : ''; ?>">Contacto</a>
    <a href="sobre-nosotros.php" class="<?= $pagina_actual == 'sobre-nosotros.php' ? 'active' : ''; ?>">Acerca de Nosotros</a>
    <a href="preguntas-frecuentes.php" class="<?= $pagina_actual == 'preguntas-frecuentes.php' ? 'active' : ''; ?>">Preguntas Frecuentes</a>
    <a href="registro.php" style="font-weight: bold; color: #38bdf8;">Registrarse</a>

    <!-- Botón de Modo Oscuro -->
    <button id="btn-modo-oscuro" onclick="alternarModo()" style="cursor: pointer; padding: 6px 12px; border-radius: 6px; border: none; background-color: #38bdf8; color: #0f172a; font-weight: bold; margin-left: auto;">
        🌙 Modo Oscuro
    </button>
</nav>