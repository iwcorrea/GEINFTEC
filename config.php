<?php
// config.php - Conexión a Supabase con depuración

// --- Conexión a la base de datos (PostgreSQL) ---
$uri = getenv('SUPABASE_URI') ?: 'postgresql://postgres.olcfippgkuhjvssnlxvn:Seguridad.123@aws-1-us-east-2.pooler.supabase.com:5432/postgres';
$parsed = parse_url($uri);
$host = $parsed['host'];
$port = $parsed['port'] ?? 5432;
$dbname = ltrim($parsed['path'], '/');
$user = $parsed['user'];
$pass = $parsed['pass'];

// Intentar conectar
$conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$pass");

if (!$conn) {
    // Mostrar error en pantalla (para depuración)
    $error_msg = "❌ Error de conexión a la base de datos: " . pg_last_error();
    die("<div style='background: #ff6b6b; color: #fff; padding: 1rem; text-align: center; font-family: sans-serif;'>$error_msg</div>");
}
pg_query($conn, "SET search_path TO public;");

// --- Configuración de Supabase Storage ---
define('SUPABASE_URL', rtrim(getenv('SUPABASE_URL') ?: 'https://olcfippgkuhjvssnlxvn.supabase.co', '/'));
define('SUPABASE_ANON_KEY', getenv('SUPABASE_ANON_KEY') ?: 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6Im9sY2ZpcHBna3VoanZzc25seHZuIiwicm9sZSI6ImFub24iLCJpYXQiOjE3ODMwNjQ4NzksImV4cCI6MjA5ODY0MDg3OX0.XtsI8ixVoGFPOiBiYhfZGrx_na6BhmGgAzgpyKYbaSk');
define('SUPABASE_BUCKET', 'geinftec');
define('SUPABASE_STORAGE_URL', SUPABASE_URL . '/storage/v1/object/public/' . SUPABASE_BUCKET . '/');

// --- Configuración de seguridad ---
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_TIMEOUT', 900);
define('SESSION_LIFETIME', 1800);
define('MAX_FILE_SIZE', 5 * 1024 * 1024);
?>