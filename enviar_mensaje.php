<?php
// ============================================================
// enviar_mensaje.php - Procesar formulario de contacto
// ============================================================
header('Content-Type: application/json');

// --- Activar registro de errores ---
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

// --- Timeout para evitar bloqueos ---
set_time_limit(30);

// --- Conexión a la base de datos ---
require_once 'config.php';

// --- Función para responder en JSON ---
function respond($success, $message, $extra = []) {
    $response = ['success' => $success, 'message' => $message] + $extra;
    echo json_encode($response);
    exit;
}

// --- Recibir datos del formulario ---
$nombre = trim($_POST['nombre'] ?? '');
$email = trim($_POST['email'] ?? '');
$telefono = trim($_POST['telefono'] ?? '');
$mensaje = trim($_POST['mensaje'] ?? '');

// --- Validar ---
if (empty($nombre) || empty($email) || empty($mensaje)) {
    respond(false, 'Todos los campos obligatorios deben estar llenos.');
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    respond(false, 'El correo electrónico no es válido.');
}

// --- Guardar en Supabase ---
$query = "INSERT INTO mensajes (nombre, email, telefono, mensaje, fecha) VALUES ($1, $2, $3, $4, NOW())";
$result = pg_query_params($conn, $query, [$nombre, $email, $telefono, $mensaje]);

if (!$result) {
    $error = pg_last_error($conn);
    error_log("Error al guardar mensaje: " . $error);
    respond(false, 'Error al guardar el mensaje. Intenta nuevamente.');
}

// --- Enviar correo con mail() nativo (sin dependencias) ---
$adminEmail = getenv('ADMIN_EMAIL') ?: 'geinftec@outlook.com';
$subject = '📩 Nuevo mensaje de contacto - GEINFTEC';
$body = "Nombre: $nombre\nEmail: $email\nTeléfono: $telefono\nMensaje: $mensaje";
$headers = "From: $email\r\nReply-To: $email\r\nContent-Type: text/plain; charset=UTF-8";

$emailEnviado = mail($adminEmail, $subject, $body, $headers);

if (!$emailEnviado) {
    error_log("Error al enviar correo con mail()");
}

// --- Respuesta ---
$response = [
    'success' => true,
    'message' => '✅ Mensaje enviado con éxito. Te contactaremos pronto.'
];

if (!$emailEnviado) {
    $response['warning'] = 'Tu mensaje fue guardado, pero no pudimos enviar la notificación por correo.';
}

echo json_encode($response);
exit;
?>