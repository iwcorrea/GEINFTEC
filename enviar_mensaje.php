<?php
// ============================================================
// enviar_mensaje.php - Procesar formulario de contacto
// ============================================================
header('Content-Type: application/json');

// --- Activar reporte de errores (ocultar en pantalla) ---
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

// --- Intentar cargar PHPMailer desde varias rutas ---
$cargado = false;
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
        $cargado = true;
        $base_path = dirname(realpath($ruta));
        break;
    }
}

// Si no se encontró PHPMailer, responder con error pero sin detener la ejecución
if (!$cargado) {
    // Intentamos cargar solo lo esencial (pero fallará el envío de correo)
    error_log("PHPMailer no encontrado en ninguna ruta");
    $mail_disponible = false;
} else {
    // Cargar SMTP y Exception desde la misma carpeta
    if (file_exists($base_path . '/SMTP.php')) {
        require_once $base_path . '/SMTP.php';
    }
    if (file_exists($base_path . '/Exception.php')) {
        require_once $base_path . '/Exception.php';
    }
    
    // Verificar que las clases existan
    if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
        $mail_disponible = true;
        use PHPMailer\PHPMailer\PHPMailer;
        use PHPMailer\PHPMailer\SMTP;
        use PHPMailer\PHPMailer\Exception;
    } else {
        error_log("Clases PHPMailer no cargadas correctamente");
        $mail_disponible = false;
    }
}

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

// --- Enviar correo de notificación (solo si PHPMailer está disponible) ---
$emailEnviado = false;
$emailError = '';

if ($mail_disponible) {
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

        // Verificar que la contraseña esté configurada
        if (empty($mail->Password)) {
            throw new Exception('La contraseña de SMTP no está configurada en las variables de entorno.');
        }

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
        $emailError = $mail->ErrorInfo;
        error_log("Error al enviar correo: " . $emailError);
    } catch (Exception $e) {
        $emailError = $e->getMessage();
        error_log("Error general al enviar correo: " . $emailError);
    }
} else {
    $emailError = 'PHPMailer no disponible.';
    error_log("PHPMailer no disponible, no se envió correo.");
}

// --- Respuesta al cliente ---
$response = [
    'success' => true,
    'message' => '✅ Mensaje enviado con éxito. Te contactaremos pronto.'
];

if (!$emailEnviado) {
    $response['warning'] = 'Tu mensaje fue guardado, pero hubo un problema al enviar la notificación.';
}

echo json_encode($response);
exit;
?>