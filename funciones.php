<?php
require_once 'config.php';

// ... (las funciones getContent, updateContent, etc. se mantienen igual) ...

// --- Función de subida con depuración ---
function uploadToSupabase($file, $filename = null) {
    // Validación de archivo
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['error' => 'Error al subir el archivo: ' . $file['error']];
    }
    if ($file['size'] > MAX_FILE_SIZE) {
        return ['error' => 'El archivo excede el tamaño máximo permitido (5 MB).'];
    }
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    if (!in_array($mimeType, $allowed)) {
        return ['error' => 'Formato de imagen no permitido. Usa JPG, PNG, GIF o WEBP.'];
    }
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = $filename ?: uniqid() . '.' . $ext;
    $filename = preg_replace('/[^a-zA-Z0-9_.-]/', '_', $filename);
    $fileContent = file_get_contents($file['tmp_name']);
    if ($fileContent === false) {
        return ['error' => 'No se pudo leer el archivo.'];
    }

    // --- Construir URL ---
    $url = SUPABASE_URL . '/storage/v1/object/' . SUPABASE_BUCKET . '/' . $filename;
    
    // --- Inicializar cURL con más opciones de depuración ---
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fileContent);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: ' . $mimeType,
        'apikey: ' . SUPABASE_ANON_KEY,
        'Authorization: Bearer ' . SUPABASE_ANON_KEY,
        'Content-Length: ' . strlen($fileContent)
    ]);
    // Para depuración, también guardamos la URL y los headers
    curl_setopt($ch, CURLOPT_VERBOSE, true);
    $verbose = fopen('php://temp', 'w+');
    curl_setopt($ch, CURLOPT_STDERR, $verbose);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    // Leer la salida de verbose
    rewind($verbose);
    $verboseLog = stream_get_contents($verbose);
    fclose($verbose);
    
    if ($httpCode === 200 || $httpCode === 201) {
        $publicUrl = SUPABASE_STORAGE_URL . $filename;
        return ['success' => $publicUrl];
    } else {
        // Construir mensaje de error detallado
        $errorMsg = "Error al subir a Supabase Storage.\n";
        $errorMsg .= "URL: $url\n";
        $errorMsg .= "HTTP Code: $httpCode\n";
        $errorMsg .= "Response: " . substr($response, 0, 500) . "\n";
        $errorMsg .= "cURL Error: $error\n";
        $errorMsg .= "Verbose Log: " . substr($verboseLog, 0, 500);
        return ['error' => $errorMsg];
    }
}
?>