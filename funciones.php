<?php
require_once 'config.php';

// --- Funciones de base de datos (existentes) ---
function getContent($seccion, $clave, $default = '') {
    global $conn;
    $result = pg_query_params($conn, 'SELECT valor FROM contenido WHERE seccion = $1 AND clave = $2', [$seccion, $clave]);
    if ($result && pg_num_rows($result) > 0) {
        $row = pg_fetch_assoc($result);
        return $row['valor'];
    }
    return $default;
}

function updateContent($seccion, $clave, $valor) {
    global $conn;
    $result = pg_query_params($conn, 'INSERT INTO contenido (seccion, clave, valor) VALUES ($1, $2, $3) ON CONFLICT (seccion, clave) DO UPDATE SET valor = EXCLUDED.valor', [$seccion, $clave, $valor]);
    return $result !== false;
}

function getSection($seccion) {
    global $conn;
    $result = pg_query_params($conn, 'SELECT clave, valor FROM contenido WHERE seccion = $1', [$seccion]);
    $data = [];
    if ($result) {
        while ($row = pg_fetch_assoc($result)) {
            $data[$row['clave']] = $row['valor'];
        }
    }
    return $data;
}

function getAllContent() {
    global $conn;
    $result = pg_query($conn, 'SELECT seccion, clave, valor FROM contenido ORDER BY seccion, clave');
    $data = [];
    if ($result) {
        while ($row = pg_fetch_assoc($result)) {
            $data[$row['seccion']][$row['clave']] = $row['valor'];
        }
    }
    return $data;
}

// --- Seguridad: CSRF ---
function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// --- Seguridad: Rate limiting para login ---
function checkLoginAttempts($ip) {
    global $conn;
    $result = pg_query_params($conn, 'SELECT attempts, last_attempt FROM login_attempts WHERE ip = $1', [$ip]);
    if ($result && pg_num_rows($result) > 0) {
        $row = pg_fetch_assoc($result);
        if ($row['attempts'] >= MAX_LOGIN_ATTEMPTS) {
            $timeSince = time() - strtotime($row['last_attempt']);
            if ($timeSince < LOGIN_TIMEOUT) {
                return false; // Bloqueado
            } else {
                // Reiniciar intentos
                pg_query_params($conn, 'DELETE FROM login_attempts WHERE ip = $1', [$ip]);
                return true;
            }
        }
    }
    return true;
}

function registerLoginAttempt($ip, $success) {
    global $conn;
    if ($success) {
        pg_query_params($conn, 'DELETE FROM login_attempts WHERE ip = $1', [$ip]);
    } else {
        $result = pg_query_params($conn, 'SELECT attempts FROM login_attempts WHERE ip = $1', [$ip]);
        if ($result && pg_num_rows($result) > 0) {
            $row = pg_fetch_assoc($result);
            $attempts = $row['attempts'] + 1;
            pg_query_params($conn, 'UPDATE login_attempts SET attempts = $1, last_attempt = NOW() WHERE ip = $2', [$attempts, $ip]);
        } else {
            pg_query_params($conn, 'INSERT INTO login_attempts (ip, attempts, last_attempt) VALUES ($1, 1, NOW())', [$ip]);
        }
    }
}

// --- Subida a Supabase Storage (mejorada) ---
function uploadToSupabase($file, $filename = null) {
    // Validar archivo
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['error' => 'Error al subir el archivo: ' . $file['error']];
    }
    
    // Validar tamaño
    if ($file['size'] > MAX_FILE_SIZE) {
        return ['error' => 'El archivo excede el tamaño máximo permitido (5 MB).'];
    }
    
    // Validar tipo MIME real
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    if (!in_array($mimeType, $allowed)) {
        return ['error' => 'Formato de imagen no permitido. Usa JPG, PNG, GIF o WEBP.'];
    }
    
    // Generar nombre único y seguro
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = $filename ?: uniqid() . '.' . $ext;
    // Sanitizar nombre
    $filename = preg_replace('/[^a-zA-Z0-9_.-]/', '_', $filename);
    
    $filepath = $file['tmp_name'];
    $fileContent = file_get_contents($filepath);
    if ($fileContent === false) {
        return ['error' => 'No se pudo leer el archivo.'];
    }
    
    // Subir a Supabase Storage
    $url = SUPABASE_URL . '/storage/v1/object/' . SUPABASE_BUCKET . '/' . $filename;
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fileContent);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: ' . $mimeType,
        'Authorization: Bearer ' . SUPABASE_ANON_KEY,
        'Content-Length: ' . strlen($fileContent)
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode === 200 || $httpCode === 201) {
        return ['success' => SUPABASE_STORAGE_URL . $filename];
    } else {
        return ['error' => 'Error al subir a Supabase Storage. Código: ' . $httpCode . ' - ' . $response];
    }
}
?>