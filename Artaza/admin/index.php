<?php
require_once __DIR__ . '/../includes/auth_guard.php';
verificarAdmin();
require_once __DIR__ . '/../config/db.php';

$titulo_pagina = "Panel de Administración - AutoMarket";
$resultado_totales = $conn->query("SELECT
    (SELECT COUNT(*) FROM vehiculos) AS vehiculos,
    (SELECT COUNT(*) FROM usuarios) AS usuarios,
    (SELECT COUNT(*) FROM favoritos) AS favoritos");
$totales = $resultado_totales->fetch_assoc();

$vehiculos_recientes = [];
$resultado_vehiculos = $conn->query("SELECT id, titulo, precio, anio FROM vehiculos ORDER BY id DESC LIMIT 5");
while ($vehiculo = $resultado_vehiculos->fetch_assoc()) {
    $vehiculos_recientes[] = $vehiculo;
}

$comentarios_muestra = [
    [
        'texto' => 'La publicación tiene todos los datos que necesitaba para comparar opciones.',
        'tema' => 'Catálogo',
    ],
    [
        'texto' => 'Me resultó fácil encontrar información sobre el estado y el equipamiento del vehículo.',
        'tema' => 'Ficha de vehículo',
    ],
    [
        'texto' => 'La atención fue clara y me ayudó a coordinar una visita.',
        'tema' => 'Atención',
    ],
];

$mensajes_estado = [
    'created' => 'El vehículo se agregó correctamente.',
    'updated' => 'Los datos del vehículo se actualizaron correctamente.',
];
$estado = $_GET['status'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $titulo_pagina ?></title>
    <link rel="stylesheet" href="../css/estilos.css">
</head>
<body>
    <header>
        <div>
            <h1 class="golden-text">Administración</h1>
            <small class="golden-text">AutoMarket · Gestión del sitio</small>
        </div>
        <div class="header-right">
            <div class="date-card">Sesión activa: <strong><?= htmlspecialchars($_SESSION['usuario_nombre'], ENT_QUOTES, 'UTF-8') ?></strong></div>
            <div class="auth-actions">
                <a class="auth-btn secondary" href="../index.php">Ver sitio público</a>
                <a class="auth-btn primary" href="../logout.php">Cerrar sesión</a>
            </div>
        </div>
    </header>

    <nav class="admin-nav" aria-label="Navegación de administración">
        <a href="index.php" class="nav-active">Resumen</a>
        <a href="#inventario">Inventario</a>
        <a href="crear.php">Agregar vehículo</a>
        <a href="#comentarios">Comentarios</a>
        <a href="../catalogo.php">Catálogo público</a>
        <button id="btn-modo-oscuro" class="auth-btn secondary" type="button" onclick="alternarModo()">🌙 Modo Oscuro</button>
    </nav>

    <main class="admin-main">
        <section class="admin-intro">
            <h2>Panel de administración</h2>
            <p>Resumen del inventario y actividad de AutoMarket.</p>
        </section>

        <?php if (isset($mensajes_estado[$estado])): ?>
            <p class="admin-notice"><?= htmlspecialchars($mensajes_estado[$estado], ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>

        <section class="grid-container admin-stats" aria-label="Resumen del sitio">
            <article class="card admin-stat-card">
                <h3>Vehículos publicados</h3>
                <p class="admin-stat-value"><?= (int) $totales['vehiculos'] ?></p>
                <a href="#inventario" class="admin-inline-link">Ver inventario</a>
            </article>
            <article class="card admin-stat-card">
                <h3>Usuarios registrados</h3>
                <p class="admin-stat-value"><?= (int) $totales['usuarios'] ?></p>
                <span class="admin-stat-note">Cuentas del sitio</span>
            </article>
            <article class="card admin-stat-card">
                <h3>Favoritos guardados</h3>
                <p class="admin-stat-value"><?= (int) $totales['favoritos'] ?></p>
                <span class="admin-stat-note">Interés en vehículos</span>
            </article>
        </section>

        <section class="admin-section" id="inventario">
            <div class="admin-section-heading">
                <div>
                    <h2>Inventario reciente</h2>
                    <p>Últimos vehículos cargados en el catálogo.</p>
                </div>
                <a href="crear.php" class="btn">+ Agregar vehículo</a>
            </div>

            <?php if (empty($vehiculos_recientes)): ?>
                <div class="card admin-empty-state">
                    <p>Todavía no hay vehículos publicados.</p>
                    <a href="crear.php" class="admin-inline-link">Cargar el primero</a>
                </div>
            <?php else: ?>
                <div class="admin-vehicle-list">
                    <?php foreach ($vehiculos_recientes as $vehiculo): ?>
                        <article class="admin-vehicle-row">
                            <div>
                                <h3><?= htmlspecialchars($vehiculo['titulo'], ENT_QUOTES, 'UTF-8') ?></h3>
                                <p><?= (int) $vehiculo['anio'] ?> · $ <?= number_format((float) $vehiculo['precio'], 0, ',', '.') ?></p>
                            </div>
                            <a class="admin-inline-link" href="editar.php?id=<?= (int) $vehiculo['id'] ?>">Editar vehículo</a>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>

        <section class="admin-section" id="comentarios">
            <div class="admin-section-heading">
                <div>
                    <h2>Comentarios de clientes</h2>
                    <p>Ejemplos de vista previa; los mensajes reales todavía no se guardan en el sitio.</p>
                </div>
            </div>
            <div class="grid-container admin-comments">
                <?php foreach ($comentarios_muestra as $comentario): ?>
                    <article class="card admin-comment">
                        <span class="admin-sample-label">Ejemplo</span>
                        <p>“<?= htmlspecialchars($comentario['texto'], ENT_QUOTES, 'UTF-8') ?>”</p>
                        <span class="admin-comment-topic"><?= htmlspecialchars($comentario['tema'], ENT_QUOTES, 'UTF-8') ?></span>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>
    </main>

    <footer>
        <p><strong>AutoMarket</strong> · <?= date('Y') ?></p>
    </footer>

    <script src="../js/jss.js"></script>
</body>
</html>