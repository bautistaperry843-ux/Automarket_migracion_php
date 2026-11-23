<?php
require_once __DIR__ . '/../includes/auth_guard.php';
verificarAdmin(); // Si no es admin, detiene la ejecución
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

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: index.php');
    exit;
}

// Obtener los datos actuales usando consulta preparada
$stmt = $pdo->prepare("SELECT * FROM vehiculos WHERE id = :id");
$stmt->execute([':id' => $id]);
$vehiculo = $stmt->fetch();

if (!$vehiculo) {
    die("Vehículo no encontrado.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo      = $_POST['titulo'] ?? '';
    $precio      = $_POST['precio'] ?? 0;
    $anio        = $_POST['anio'] ?? 2024;
    $kilometros  = $_POST['kilometros'] ?? 0;
    $transmision = $_POST['transmision'] ?? 'Manual';
    $combustible = $_POST['combustible'] ?? 'Nafta';
    $descripcion = $_POST['descripcion'] ?? '';

    if (!empty($titulo)) {
        // Consulta Preparada de Actualización (Update seguro)
        $sql = "UPDATE vehiculos SET titulo = :titulo, precio = :precio, anio = :anio, kilometros = :kilometros, transmision = :transmision, combustible = :combustible, descripcion = :descripcion WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':titulo'      => $titulo,
            ':precio'      => $precio,
            ':anio'        => $anio,
            ':kilometros'  => $kilometros,
            ':transmision' => $transmision,
            ':combustible' => $combustible,
            ':descripcion' => $descripcion,
            ':id'          => $id
        ]);
        header('Location: index.php?status=updated');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Vehículo - AutoMarket</title>
</head>
<body style="background-color: #f8f9fa; color: #333; font-family: Arial, sans-serif; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background: #fff; padding: 25px; border-radius: 8px; border: 1px solid #ddd;">
        <h2 style="color: #b8860b; margin-bottom: 20px;">Editar Vehículo (CRUD - Update)</h2>
        <form action="" method="POST">
            <p><strong>Título / Modelo:</strong><br><input type="text" name="titulo" value="<?= htmlspecialchars($vehiculo['titulo']) ?>" required style="width: 100%; padding: 8px;"></p>
            <p><strong>Precio (USD):</strong><br><input type="number" step="0.01" name="precio" value="<?= htmlspecialchars($vehiculo['precio']) ?>" required style="width: 100%; padding: 8px;"></p>
            <p><strong>Año:</strong><br><input type="number" name="anio" value="<?= htmlspecialchars($vehiculo['anio']) ?>" required style="width: 100%; padding: 8px;"></p>
            <p><strong>Kilómetros:</strong><br><input type="number" name="kilometros" value="<?= htmlspecialchars($vehiculo['kilometros']) ?>" required style="width: 100%; padding: 8px;"></p>
            <p><strong>Transmisión:</strong><br>
                <select name="transmision" style="width: 100%; padding: 8px;">
                    <option value="Manual" <?= ($vehiculo['transmision'] === 'Manual') ? 'selected' : '' ?>>Manual</option>
                    <option value="Automática" <?= ($vehiculo['transmision'] === 'Automática') ? 'selected' : '' ?>>Automática</option>
                </select>
            </p>
            <p><strong>Combustible:</strong><br>
                <select name="combustible" style="width: 100%; padding: 8px;">
                    <option value="Nafta" <?= ($vehiculo['combustible'] === 'Nafta') ? 'selected' : '' ?>>Nafta</option>
                    <option value="Diesel" <?= ($vehiculo['combustible'] === 'Diesel') ? 'selected' : '' ?>>Diesel</option>
                </select>
            </p>
            <p><strong>Descripción:</strong><br><textarea name="descripcion" rows="4" style="width: 100%; padding: 8px;"><?= htmlspecialchars($vehiculo['descripcion']) ?></textarea></p>
            
            <button type="submit" style="background: #ffc107; color: #000; font-weight: bold; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer;">Actualizar Vehículo</button>
            <a href="index.php" style="margin-left: 15px; color: #555; text-decoration: underline;">Cancelar</a>
        </form>
    </div>
</body>
</html>