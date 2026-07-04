<?php
// config.php - Configuración general y seguridad

// --- Configuración de sesión ANTES de iniciar la sesión ---
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);     // Solo HTTPS (Render lo usa)
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_samesite', 'Strict');
ini_set('session.gc_maxlifetime', 1800); // 30 minutos

session_start();

// Regenerar ID de sesión periódicamente
if (!isset($_SESSION['created'])) {
    $_SESSION['created'] = time();
} elseif (time() - $_SESSION['created'] > 1800) {
    session_regenerate_id(true);
    $_SESSION['created'] = time();
}

// --- Conexión a la base de datos (PostgreSQL) ---
$uri = getenv('SUPABASE_URI') ?: 'postgresql://postgres.olcfippgkuhjvssnlxvn:Seguridad.123@aws-1-us-east-2.pooler.supabase.com:5432/postgres';
$parsed = parse_url($uri);
$host = $parsed['host'];
$port = $parsed['port'] ?? 5432;
$dbname = ltrim($parsed['path'], '/');
$user = $parsed['user'];
$pass = $parsed['pass'];

$conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$pass");
if (!$conn) {
    die("❌ Error de conexión a la base de datos: " . pg_last_error());
}
pg_query($conn, "SET search_path TO public;");

// --- Configuración de Supabase Storage ---
define('SUPABASE_URL', getenv('SUPABASE_URL') ?: 'https://olcfippgkuhjvssnlxvn.supabase.co');
define('SUPABASE_ANON_KEY', getenv('SUPABASE_ANON_KEY') ?: 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6Im9sY2ZpcHBna3VoanZzc25seHZuIiwicm9sZSI6ImFub24iLCJpYXQiOjE3MTk5ODU5MjMsImV4cCI6MjAzNTU2MTkyM30.0BqjQk0fS7Jd2jqQeX7lQ6eXlQ6eXlQ6eXlQ6eXlQ6eXlQ6eXlQ6eXlQ6eXlQ');
define('SUPABASE_BUCKET', 'geinftec');
define('SUPABASE_STORAGE_URL', SUPABASE_URL . '/storage/v1/object/public/' . SUPABASE_BUCKET . '/');

// --- Configuración de seguridad ---
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_TIMEOUT', 900);
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5 MB
?>