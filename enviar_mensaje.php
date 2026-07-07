<?php
// ============================================================
// enviar_mensaje.php - Procesar formulario de contacto
// Usa PHPMailer con variables de entorno (OUTLOOK)
// ============================================================
header('Content-Type: application/json');

// --- Activar registro de errores ---
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

// --- Timeout para evitar bloqueos ---
set_time_limit(15); // 15 segundos máximo

// --- Conexión a la base de datos ---
require_once 'config.php';

// --- Cargar PHPMailer ---
require_once 'phpmailer/src/PHPMailer.php';
require_once 'phpmailer/src/SMTP.php';
require_once 'phpmailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// --- Función para responder ---
function respond($success, $message, $extra = []) {
    $response = ['success' => $success, 'message' => $message] + $extra;
    echo json_encode($response);
    exit;
}

// --- Recibir datos ---
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
    error_log("Error al guardar mensaje: " . pg_last_error($conn));
    respond(false, 'Error al guardar el mensaje. Intenta nuevamente.');
}

// --- Enviar correo con PHPMailer (OUTLOOK) ---
$emailEnviado = false;
$emailError = '';

try {
    $mail = new PHPMailer(true);

    // Configuración SMTP desde variables de entorno
    $mail->isSMTP();
    $mail->Host       = getenv('SMTP_HOST') ?: 'smtp.office365.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = getenv('SMTP_USER') ?: 'geinftec@outlook.com';
    $mail->Password   = getenv('SMTP_PASS') ?: ''; // ¡Debe estar definida en Render!
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = getenv('SMTP_PORT') ?: 587;
    $mail->Timeout    = 10; // 10 segundos de timeout

    // Verificar que la contraseña esté configurada
    if (empty($mail->Password)) {
        throw new Exception('La contraseña de SMTP no está configurada en las variables de entorno.');
    }

    // Destinatarios
    $mail->setFrom($email, $nombre);
    $mail->addAddress(getenv('ADMIN_EMAIL') ?: 'geinftec@outlook.com', 'Administrador GEINFTEC');
    $mail->addReplyTo($email, $nombre);

    // Contenido
    $mail->isHTML(true);
    $mail->Subject = '📩 Nuevo mensaje de contacto - GEINFTEC';
    $mail->Body = "
        <h2>📨 Nuevo mensaje de contacto</h2>
        <p><strong>👤 Nombre:</strong> $nombre</p>
        <p><strong>📧 Email:</strong> $email</p>
        <p><strong>📱 Teléfono:</strong> " . ($telefono ?: 'No especificado') . "</p>
        <p><strong>💬 Mensaje:</strong></p>
        <p style='background:#f4f4f4; padding:1rem; border-radius:8px;'>" . nl2br(htmlspecialchars($mensaje)) . "</p>
        <hr>
        <p><small>Enviado desde el formulario de contacto de GEINFTEC S.A.S.</small></p>
    ";
    $mail->AltBody = "Nuevo mensaje de contacto\n\nNombre: $nombre\nEmail: $email\nTeléfono: $telefono\nMensaje: $mensaje";

    $mail->send();
    $emailEnviado = true;

} catch (Exception $e) {
    $emailError = $mail->ErrorInfo;
    error_log("Error PHPMailer: " . $emailError);
}

// --- Respuesta ---
$response = [
    'success' => true,
    'message' => '✅ Mensaje enviado con éxito. Te contactaremos pronto.'
];

if (!$emailEnviado) {
    $response['warning'] = 'Tu mensaje fue guardado, pero no pudimos enviar la notificación por correo.';
    $response['debug'] = $emailError; // Solo para depuración (quita en producción)
}

echo json_encode($response);
exit;
?>