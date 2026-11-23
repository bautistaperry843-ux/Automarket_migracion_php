<?php
require_once __DIR__ . '/../includes/auth_guard.php';
verificarAdmin();
$host = 'localhost';
$db = 'automarket';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];
try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    die("Error crítico de conexión: " . $e->getMessage());
}

$url_destacada = '../catalogo.php';
$resultado_destacado = $pdo->query("SELECT vehiculo_id, COUNT(*) AS total_favs FROM favoritos GROUP BY vehiculo_id ORDER BY total_favs DESC LIMIT 1");
$vehiculo_destacado = $resultado_destacado->fetch();
if ($vehiculo_destacado) {
    $url_destacada = '../vehiculo.php?id=' . (int) $vehiculo_destacado['vehiculo_id'];
}

$mensaje = '';
$mensaje_tipo = 'error';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo'] ?? '');
    $precio = filter_var($_POST['precio'] ?? null, FILTER_VALIDATE_FLOAT);
    $anio = filter_var($_POST['anio'] ?? null, FILTER_VALIDATE_INT);
    $kilometros = filter_var($_POST['kilometros'] ?? null, FILTER_VALIDATE_INT);
    $transmision = $_POST['transmision'] ?? '';
    $combustible = $_POST['combustible'] ?? '';
    $descripcion = trim($_POST['descripcion'] ?? '');
    $opciones_transmision = ['Manual', 'Automática'];
    $opciones_combustible = ['Nafta', 'Diesel', 'Híbrido/Eléctrico'];

    if ($titulo === '' || $precio === false || $precio < 0 || $anio === false || $anio < 1900 || $anio > (int) date('Y') + 1 || $kilometros === false || $kilometros < 0) {
        $mensaje = 'Revisá el título, el precio, el año y el kilometraje.';
    } elseif (!in_array($transmision, $opciones_transmision, true) || !in_array($combustible, $opciones_combustible, true)) {
        $mensaje = 'Seleccioná una transmisión y un combustible válidos.';
    } elseif (!isset($_FILES['foto']) || $_FILES['foto']['error'] !== UPLOAD_ERR_OK) {
        $mensaje = 'Seleccioná una foto del vehículo para continuar.';
    } elseif ($_FILES['foto']['size'] > 5 * 1024 * 1024) {
        $mensaje = 'La foto no puede superar los 5 MB.';
    } else {
        $tipos_foto = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
        ];
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $tipo_foto = $finfo->file($_FILES['foto']['tmp_name']);

        if (!isset($tipos_foto[$tipo_foto])) {
            $mensaje = 'Usá una imagen JPG, PNG o WebP.';
        } else {
            $directorio_subidas = __DIR__ . '/../uploads';
            if (!is_dir($directorio_subidas) && !mkdir($directorio_subidas, 0755, true) && !is_dir($directorio_subidas)) {
                $mensaje = 'No se pudo preparar el espacio para guardar la foto.';
            } else {
                $nombre_foto = bin2hex(random_bytes(16)) . '.' . $tipos_foto[$tipo_foto];
                $ruta_foto = $directorio_subidas . '/' . $nombre_foto;

                if (!move_uploaded_file($_FILES['foto']['tmp_name'], $ruta_foto)) {
                    $mensaje = 'No se pudo guardar la foto. Intentá nuevamente.';
                } else {
                    try {
                        $sql = "INSERT INTO vehiculos (usuario_id, titulo, precio, anio, kilometros, transmision, combustible, descripcion, imagen)
                                VALUES (:usuario_id, :titulo, :precio, :anio, :kilometros, :transmision, :combustible, :descripcion, :imagen)";
                        $stmt = $pdo->prepare($sql);
                        $stmt->execute([
                            ':usuario_id' => (int) $_SESSION['usuario_id'],
                            ':titulo' => $titulo,
                            ':precio' => $precio,
                            ':anio' => $anio,
                            ':kilometros' => $kilometros,
                            ':transmision' => $transmision,
                            ':combustible' => $combustible,
                            ':descripcion' => $descripcion,
                            ':imagen' => $nombre_foto,
                        ]);
                        header('Location: index.php?status=created');
                        exit;
                    } catch (\PDOException $e) {
                        if (is_file($ruta_foto)) {
                            unlink($ruta_foto);
                        }
                        $mensaje = 'No se pudo publicar el vehículo. Revisá los datos e intentá nuevamente.';
                    }
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Vehículo - AutoMarket</title>
    <link rel="stylesheet" href="../css/estilos.css">
</head>
<body>
    <header>
        <div>
            <h1 class="golden-text">Agregar vehículo</h1>
            <small class="golden-text">Administración · AutoMarket</small>
        </div>
        <div class="header-right">
            <div class="date-card">Sesión activa: <strong><?= htmlspecialchars($_SESSION['usuario_nombre'], ENT_QUOTES, 'UTF-8') ?></strong></div>
            <div class="auth-actions">
                <a class="auth-btn secondary" href="index.php">Volver al panel</a>
                <a class="auth-btn primary" href="../logout.php">Cerrar sesión</a>
            </div>
        </div>
    </header>

    <nav aria-label="Navegación de administración">
        <details class="nav-dropdown">
            <summary class="golden-text">Catálogo de Vehículos ▾</summary>
            <div class="dropdown-content">
                <a href="../catalogo.php">Ver Todos los Autos</a>
                <a href="<?= htmlspecialchars($url_destacada, ENT_QUOTES, 'UTF-8') ?>">Ver Oferta Destacada</a>
            </div>
        </details>
        <a href="../index.php" class="golden-text">Ir al Inicio</a>
        <a href="../vender%20auto.php" class="golden-text">Vender mi Auto</a>
        <a href="../curso.php" class="golden-text">Curso Mecánica</a>
        <a href="../favoritos.php" class="golden-text">Favoritos</a>
        <a href="../contacto.php" class="golden-text">Contacto</a>
        <a href="../sobre%20nosotros.php" class="golden-text">Acerca de Nosotros</a>
        <a href="../preguntas%20frecuentes.php" class="golden-text">Preguntas Frecuentes</a>
        <a href="index.php" class="golden-text">Resumen</a>
        <a href="crear.php" class="golden-text nav-active">Agregar vehículo</a>
        <button id="btn-modo-oscuro" class="btn-modo-oscuro" type="button" onclick="alternarModo()">🌙 Modo Oscuro</button>
    </nav>

    <main class="sell-main">
        <section class="sell-intro">
            <span class="sell-eyebrow">INVENTARIO · NUEVA PUBLICACIÓN</span>
            <h2>Agregar vehículo</h2>
            <p>Cargá los datos y una foto para sumar una nueva unidad al catálogo.</p>
        </section>

        <?php if ($mensaje !== ''): ?>
            <div class="sell-alert <?= htmlspecialchars($mensaje_tipo, ENT_QUOTES, 'UTF-8') ?>" role="alert">
                <?= htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <div class="sell-layout">
            <form class="card sell-form" action="" method="POST" enctype="multipart/form-data">
                <div class="sell-form-heading">
                    <h3>Datos del vehículo</h3>
                    <p>Completá la información principal de la unidad.</p>
                </div>

                <div class="sell-form-grid">
                    <label class="sell-field sell-field-wide" for="titulo">
                        Título o modelo
                        <input type="text" id="titulo" name="titulo" value="<?= htmlspecialchars($_POST['titulo'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="Ej.: Toyota Corolla XEI" required>
                    </label>

                    <label class="sell-field" for="precio">
                        Precio (USD)
                        <input type="number" id="precio" name="precio" min="0" step="0.01" value="<?= htmlspecialchars($_POST['precio'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="Ej.: 18500" required>
                    </label>

                    <label class="sell-field" for="anio">
                        Año
                        <input type="number" id="anio" name="anio" min="1900" max="<?= (int) date('Y') + 1 ?>" value="<?= htmlspecialchars($_POST['anio'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="Ej.: 2022" required>
                    </label>

                    <label class="sell-field" for="kilometros">
                        Kilómetros
                        <input type="number" id="kilometros" name="kilometros" min="0" value="<?= htmlspecialchars($_POST['kilometros'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="Ej.: 45000" required>
                    </label>

                    <label class="sell-field" for="transmision">
                        Transmisión
                        <select id="transmision" name="transmision">
                            <option value="Manual" <?= ($_POST['transmision'] ?? 'Manual') === 'Manual' ? 'selected' : '' ?>>Manual</option>
                            <option value="Automática" <?= ($_POST['transmision'] ?? '') === 'Automática' ? 'selected' : '' ?>>Automática</option>
                        </select>
                    </label>

                    <label class="sell-field" for="combustible">
                        Combustible
                        <select id="combustible" name="combustible">
                            <option value="Nafta" <?= ($_POST['combustible'] ?? 'Nafta') === 'Nafta' ? 'selected' : '' ?>>Nafta</option>
                            <option value="Diesel" <?= ($_POST['combustible'] ?? '') === 'Diesel' ? 'selected' : '' ?>>Diesel</option>
                            <option value="Híbrido/Eléctrico" <?= ($_POST['combustible'] ?? '') === 'Híbrido/Eléctrico' ? 'selected' : '' ?>>Híbrido/Eléctrico</option>
                        </select>
                    </label>

                    <label class="sell-field sell-field-wide" for="descripcion">
                        Descripción y características
                        <textarea id="descripcion" name="descripcion" rows="4" placeholder="Equipamiento, estado general y documentación."><?= htmlspecialchars($_POST['descripcion'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                    </label>
                </div>

                <div class="sell-photo-section">
                    <div>
                        <h3>Foto principal</h3>
                        <p>JPG, PNG o WebP · máximo 5 MB</p>
                    </div>
                    <div class="upload-file-wrap">
                        <label for="input-foto" class="custom-file-btn">Seleccionar imagen</label>
                        <input type="file" name="foto" id="input-foto" accept="image/jpeg,image/png,image/webp" required>
                        <span class="file-name" id="file-name">Ninguna imagen seleccionada</span>
                    </div>
                    <img id="vista-previa" class="sell-photo-preview" src="#" alt="Vista previa de la foto seleccionada" hidden>
                </div>

                <div class="sell-form-actions">
                    <span>La publicación aparecerá en el catálogo.</span>
                    <button type="submit" class="btn">Guardar vehículo</button>
                </div>
            </form>

            <aside class="sell-aside">
                <section class="sell-aside-section">
                    <h3>Antes de publicar</h3>
                    <ul>
                        <li>Verificá que el precio y el kilometraje sean correctos.</li>
                        <li>Incluí marca, modelo y versión en el título.</li>
                        <li>Elegí una foto clara y actual del vehículo.</li>
                        <li>Describí el estado y el equipamiento con precisión.</li>
                    </ul>
                </section>
                <section class="sell-aside-section">
                    <h3>Administración</h3>
                    <p>Después de guardar, vas a volver al panel para revisar el inventario.</p>
                    <a href="index.php" class="admin-inline-link">Volver al panel</a>
                </section>
            </aside>
        </div>
    </main>

    <footer>
        <p><strong>AutoMarket</strong> · <?= date('Y') ?></p>
    </footer>

    <script src="../js/jss.js"></script>
    <script>
        const inputFoto = document.getElementById('input-foto');
        const nombreFoto = document.getElementById('file-name');
        const vistaPrevia = document.getElementById('vista-previa');
        let urlVistaPrevia = '';

        inputFoto.addEventListener('change', (event) => {
            const [foto] = event.target.files;
            if (urlVistaPrevia) URL.revokeObjectURL(urlVistaPrevia);

            if (foto) {
                nombreFoto.textContent = foto.name;
                urlVistaPrevia = URL.createObjectURL(foto);
                vistaPrevia.src = urlVistaPrevia;
                vistaPrevia.hidden = false;
            } else {
                nombreFoto.textContent = 'Ninguna imagen seleccionada';
                vistaPrevia.hidden = true;
            }
        });
    </script>
</body>
</html>