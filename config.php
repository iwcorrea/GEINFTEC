<?php
// config.php - Conexión a PostgreSQL con PDO usando variables de entorno

// Obtener variables de entorno (definidas en Render)
$host = getenv('DB_HOST') ?: 'localhost';
$port = getenv('DB_PORT') ?: '5432';
$dbname = getenv('DB_NAME') ?: 'geinftec_db';
$user = getenv('DB_USER') ?: 'postgres';
$password = getenv('DB_PASS') ?: '';

// DSN para PostgreSQL
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

// Guardar la conexión en una variable global para usarla en funciones.php
$conn = $pdo;
?>