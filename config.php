<?php
// config.php - Conexión a Supabase usando la URI completa (más simple)

// Define tu URI de Supabase (cópiala de la sección "Session pooler" > "URI")
// Recuerda reemplazar [YOUR-PASSWORD] con tu contraseña real
define('SUPABASE_URI', 'postgresql://postgres.olcfippgkuhjvssnlxvn:Seguridad.123@aws-1-us-east-2.pooler.supabase.com:5432/postgres');

try {
    // Usamos la URI directamente como DSN
    $pdo = new PDO(SUPABASE_URI);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
} catch (PDOException $e) {
    die("❌ Error de conexión a la base de datos: " . $e->getMessage());
}

$conn = $pdo;
?>