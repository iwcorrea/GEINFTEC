<?php
// config.php - Conexión a PostgreSQL con PDO usando variables de entorno

$host = getenv('DB_HOST') ?: 'localhost';
$port = getenv('DB_PORT') ?: '5432';
$dbname = getenv('DB_NAME') ?: 'geinftec_db';
$user = getenv('DB_USER') ?: 'postgres';
$password = getenv('DB_PASS') ?: '';

$dsn = "pgsql:host=$host;port=$port;dbname=$dbname";

try {
    $pdo = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $e) {
    die("❌ Error de conexión a la base de datos: " . $e->getMessage());
}

$conn = $pdo;

// (Opcional) Contraseña para el admin (hash de "admin123")
// Si quieres cambiarla, genera un nuevo hash con password_hash('tucontraseña', PASSWORD_DEFAULT)
define('ADMIN_PASSWORD_HASH', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');
?>