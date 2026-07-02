<?php
// config.php - Conexión a PostgreSQL mediante PDO y variables de entorno

$host = getenv('DB_HOST') ?: 'localhost';
$port = getenv('DB_PORT') ?: '5432';
$db   = getenv('DB_NAME') ?: 'geinftec_db';
$user = getenv('DB_USER') ?: 'admin_user';
$pass = getenv('DB_PASS') ?: '';

// Mostrar errores para depuración (en producción desactivar)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// DSN para PostgreSQL
$dsn = "pgsql:host=$host;port=$port;dbname=$db;";

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $e) {
    die("❌ Error de conexión a la base de datos PostgreSQL: " . $e->getMessage() .
        "<br>Host: $host | Usuario: $user | Base: $db");
}

// Guardar la conexión en una variable global para usar en funciones
$GLOBALS['pdo'] = $pdo;

// Definir constantes para acceso rápido
define('DB_HOST', $host);
define('DB_PORT', $port);
define('DB_NAME', $db);
define('DB_USER', $user);
define('DB_PASS', $pass);
?>