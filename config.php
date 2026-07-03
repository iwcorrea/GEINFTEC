<?php
// config.php - Conexión a Supabase usando la URI completa desde variable de entorno

// Obtener la URI desde las variables de entorno (definida en Render)
$uri = getenv('SUPABASE_URI') ?: 'postgresql://postgres.olcfippgkuhjvssnlxvn:Seguridad.123@aws-1-us-east-2.pooler.supabase.com:5432/postgres';

try {
    $pdo = new PDO($uri);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
} catch (PDOException $e) {
    die("❌ Error de conexión a la base de datos: " . $e->getMessage());
}

$conn = $pdo;
?>