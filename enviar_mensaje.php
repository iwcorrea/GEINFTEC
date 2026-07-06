<?php
// ============================================================
// enviar_mensaje.php - Procesar formulario de contacto
// Guarda en Supabase y envía notificación por correo
// ============================================================
header('Content-Type: application/json');

// --- Incluir PHPMailer (ajusta la ruta según tu estructura) ---
require_once 'phpmailer/src/PHPMailer.php';
require_once 'phpmailer/src/SMTP.php';
require_once 'phpmailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// --- Conexión a la base de datos ---
require_once 'config.php';

// --- Recibir datos del formulario ---
$nombre = trim($_POST['nombre'] ?? '');
$email = trim($_POST['email'] ?? '');
$telefono = trim($_POST['telefono'] ?? '');
$mensaje = trim($_POST['mensaje'] ?? '');

// --- Validar campos obligatorios ---
if (empty($nombre) || empty($email) || empty($mensaje)) {
    echo json_encode([
        'success' => false,
        'error' => 'Todos los campos obligatorios deben estar llenos.'
    ]);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        'success' => false,
        'error' => 'El correo electrónico no es válido.'
    ]);
    exit;
}

// --- Guardar en Supabase (tabla mensajes) ---
$query = "INSERT INTO mensajes (nombre, email, telefono, mensaje, fecha) VALUES ($1, $2, $3, $4, NOW())";
$result = pg_query_params($conn, $query, [$nombre, $email, $telefono, $mensaje]);

if (!$result) {
    error_log("Error al guardar mensaje: " . pg_last_error($conn));
    echo json_encode([
        'success' => false,
        'error' => 'Error al guardar el mensaje. Intenta nuevamente.'
    ]);
    exit;
}

// --- Enviar correo de notificación al administrador ---
$emailEnviado = false;

try {
    $mail = new PHPMailer(true);

    // Configuración SMTP desde variables de entorno (definidas en Render)
    $mail->isSMTP();
    $mail->Host       = getenv('SMTP_HOST') ?: 'smtp.office365.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = getenv('SMTP_USER') ?: 'geinftec@outlook.com';
    $mail->Password   = getenv('SMTP_PASS') ?: 'tu_contraseña';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = getenv('SMTP_PORT') ?: 587;

    // Destinatarios
    $mail->setFrom($email, $nombre);
    $mail->addAddress(getenv('ADMIN_EMAIL') ?: 'geinftec@outlook.com', 'Administrador GEINFTEC');
    $mail->addReplyTo($email, $nombre);

    // Contenido del correo
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
        <p><small>Fecha: " . date('d/m/Y H:i') . "</small></p>
    ";
    $mail->AltBody = "Nuevo mensaje de contacto\n\nNombre: $nombre\nEmail: $email\nTeléfono: $telefono\nMensaje: $mensaje";

    $mail->send();
    $emailEnviado = true;

} catch (Exception $e) {
    error_log("Error al enviar correo: " . $mail->ErrorInfo);
    // No detenemos la ejecución, ya que el mensaje se guardó en BD
}

// --- Respuesta al cliente ---
$respuesta = [
    'success' => true,
    'message' => '✅ Mensaje enviado con éxito. Te contactaremos pronto.',
    'email_enviado' => $emailEnviado
];

if (!$emailEnviado) {
    $respuesta['warning'] = 'Tu mensaje fue guardado, pero hubo un problema al enviar la notificación.';
}

echo json_encode($respuesta);
exit;
?>