<?php
// config.php - Conexión a PostgreSQL usando pg_connect (alternativa a PDO)

// Obtener la URI desde las variables de entorno o usar la definida directamente
$uri = getenv('SUPABASE_URI') ?: 'postgresql://postgres.olcfippgkuhjvssnlxvn:Seguridad.123@aws-1-us-east-2.pooler.supabase.com:5432/postgres';

// Parsear la URI manualmente
$parsed = parse_url($uri);
$host = $parsed['host'];
$port = $parsed['port'] ?? 5432;
$dbname = ltrim($parsed['path'], '/');
$user = $parsed['user'];
$pass = $parsed['pass'];

// Intentar conexión con la extensión pgsql
$conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$pass");

if (!$conn) {
    die("❌ Error de conexión a la base de datos: " . pg_last_error());
}

// Establecer el esquema de búsqueda (opcional)
pg_query($conn, "SET search_path TO public;");

// Guardar la conexión en una variable global para usarla en funciones.php
$db_conn = $conn;
?>