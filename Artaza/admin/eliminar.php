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

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // Sentencia Preparada obligatoria para el borrado seguro
    $stmt = $pdo->prepare("DELETE FROM vehiculos WHERE id = :id");
    $stmt->execute([':id' => $id]);
}

header('Location: index.php');
exit;
?>