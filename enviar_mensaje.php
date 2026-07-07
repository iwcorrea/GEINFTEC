<?php
// ============================================================
// enviar_mensaje.php - Procesar formulario de contacto
// Guarda en Supabase y envía notificación por correo
// ============================================================
header('Content-Type: application/json');

// --- Activar registro de errores (sin mostrar en pantalla) ---
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

// --- Intentar cargar PHPMailer desde varias rutas ---
$phpmailer_loaded = false;
$posibles_rutas = [
    'phpmailer/src/PHPMailer.php',
    'src/PHPMailer.php',
    '../phpmailer/src/PHPMailer.php',
    'vendor/phpmailer/phpmailer/src/PHPMailer.php',
    'librerias/phpmailer/src/PHPMailer.php'
];

foreach ($posibles_rutas as $ruta) {
    if (file_exists($ruta)) {
        require_once $ruta;
        $base = dirname(realpath($ruta));
        require_once $base . '/SMTP.php';
        require_once $base . '/Exception.php';
        $phpmailer_loaded = true;
        break;
    }
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

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

// --- Validar campos obligatorios ---
if (empty($nombre) || empty($email) || empty($mensaje)) {
    respond(false, 'Todos los campos obligatorios deben estar llenos.');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    respond(false, 'El correo electrónico no es válido.');
}

// --- Guardar en Supabase (tabla mensajes) ---
$query = "INSERT INTO mensajes (nombre, email, telefono, mensaje, fecha) VALUES ($1, $2, $3, $4, NOW())";
$result = pg_query_params($conn, $query, [$nombre, $email, $telefono, $mensaje]);

if (!$result) {
    error_log("Error al guardar mensaje: " . pg_last_error($conn));
    respond(false, 'Error al guardar el mensaje. Intenta nuevamente.');
}

// --- Enviar correo de notificación (si PHPMailer está disponible) ---
$emailEnviado = false;
$emailError = '';

if ($phpmailer_loaded) {
    try {
        $mail = new PHPMailer(true);

        // Configuración SMTP desde variables de entorno
        $mail->isSMTP();
        $mail->Host       = getenv('SMTP_HOST') ?: 'smtp.office365.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = getenv('SMTP_USER') ?: 'geinftec@outlook.com';
        $mail->Password   = getenv('SMTP_PASS') ?: '';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = getenv('SMTP_PORT') ?: 587;

        if (empty($mail->Password)) {
            throw new Exception('La contraseña de SMTP no está configurada.');
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
        error_log("Error al enviar correo: " . $emailError);
    }
} else {
    // Fallback: usar mail() nativo (menos fiable, pero intentarlo)
    $subject = '📩 Nuevo mensaje de contacto - GEINFTEC';
    $body = "Nombre: $nombre\nEmail: $email\nTeléfono: $telefono\nMensaje: $mensaje";
    $headers = "From: $email\r\nReply-To: $email";
    if (mail(getenv('ADMIN_EMAIL') ?: 'geinftec@outlook.com', $subject, $body, $headers)) {
        $emailEnviado = true;
    } else {
        $emailError = 'Error al enviar con mail() nativo.';
        error_log($emailError);
    }
}

// --- Respuesta al cliente ---
$response = [
    'success' => true,
    'message' => '✅ Mensaje enviado con éxito. Te contactaremos pronto.'
];

if (!$emailEnviado) {
    $response['warning'] = 'Tu mensaje fue guardado, pero hubo un problema al enviar la notificación.';
    $response['debug'] = $emailError; // Solo para depuración (quita en producción)
}

echo json_encode($response);
exit;
?>