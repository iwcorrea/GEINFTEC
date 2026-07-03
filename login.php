<?php
session_start();
require_once 'config.php';

// Redirigir si ya está logueado
if (isset($_SESSION['admin_logged']) && $_SESSION['admin_logged'] === true) {
    header('Location: admin.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    // Leer contraseña desde variable de entorno (o usar fallback)
    $admin_pass = getenv('ADMIN_PASSWORD') ?: 'admin123';
    
    // Verificar contraseña
    if ($password === $admin_pass) {
        // Regenerar ID de sesión para prevenir fijación
        session_regenerate_id(true);
        $_SESSION['admin_logged'] = true;
        $_SESSION['login_time'] = time();
        header('Location: admin.php');
        exit;
    } else {
        $error = 'Contraseña incorrecta.';
        // Opcional: registrar intento fallido (log)
        error_log("Intento de login fallido desde IP: " . $_SERVER['REMOTE_ADDR']);
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión - GEINFTEC</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        /* (Estilos iguales a los anteriores, solo añado uno para el botón volver) */
        body { font-family: 'Inter', sans-serif; background: #0b132b; color: #f8fafc; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; padding: 1rem; }
        .login-box { background: #1c2541; padding: 2.5rem; border-radius: 16px; box-shadow: 0 20px 60px rgba(0,0,0,0.6); width: 100%; max-width: 400px; border: 1px solid rgba(0,245,212,0.15); position: relative; }
        .btn-back { position: absolute; top: 1rem; left: 1rem; color: #b0b8d1; text-decoration: none; font-size: 0.9rem; transition: color 0.3s; }
        .btn-back:hover { color: #00f5d4; }
        .login-box h1 { color: #00f5d4; text-align: center; margin-bottom: 0.5rem; font-size: 2rem; font-weight: 700; }
        .login-box .sub { text-align: center; color: #b0b8d1; margin-bottom: 2rem; font-size: 0.95rem; }
        .login-box input { width: 100%; padding: 0.9rem 1rem; margin-bottom: 1.2rem; border: 2px solid transparent; border-radius: 10px; background: #0b132b; color: #fff; font-family: 'Inter', sans-serif; font-size: 1rem; transition: border 0.3s, box-shadow 0.3s; }
        .login-box input:focus { outline: none; border-color: #00f5d4; box-shadow: 0 0 20px rgba(0,245,212,0.15); }
        .login-box button { width: 100%; padding: 0.9rem; background: #00f5d4; color: #0b132b; border: none; border-radius: 50px; font-weight: 700; font-size: 1.05rem; cursor: pointer; transition: background 0.3s, transform 0.2s; }
        .login-box button:hover { background: #00d4b8; transform: scale(1.02); }
        .error { color: #ff6b6b; text-align: center; margin-bottom: 1rem; padding: 0.6rem; background: rgba(255,107,107,0.1); border-radius: 8px; font-weight: 600; }
        .login-box .footer-text { text-align: center; color: #b0b8d1; font-size: 0.85rem; margin-top: 1.5rem; }
        .login-box .footer-text strong { color: #00f5d4; }
        .login-box .logo-icon { text-align: center; font-size: 3rem; margin-bottom: 0.5rem; }
        .password-toggle { position: relative; }
        .password-toggle input { padding-right: 2.5rem; }
        .toggle-eye { position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #b0b8d1; }
        .toggle-eye:hover { color: #00f5d4; }
        .error-shake { animation: shake 0.3s; }
        @keyframes shake { 0%,100%{transform:translateX(0)} 25%{transform:translateX(-5px)} 75%{transform:translateX(5px)} }
    </style>
</head>
<body>
    <div class="login-box">
        <a href="index.php" class="btn-back">← Volver al sitio</a>
        <div class="logo-icon">🔐</div>
        <h1>GEINFTEC</h1>
        <p class="sub">Acceso al panel de administración</p>
        <?php if ($error): ?>
            <div class="error error-shake"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form method="post" id="loginForm">
            <div class="password-toggle">
                <input type="password" name="password" id="password" placeholder="Contraseña" required autofocus />
                <span class="toggle-eye" id="toggleEye">👁️</span>
            </div>
            <button type="submit">Ingresar</button>
        </form>
        <div class="footer-text">
            Contraseña definida en <strong>ADMIN_PASSWORD</strong> (o fallback)
        </div>
    </div>

    <script>
        // Toggle de visibilidad de contraseña
        const toggleEye = document.getElementById('toggleEye');
        const passwordInput = document.getElementById('password');
        toggleEye.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.textContent = type === 'password' ? '👁️' : '🙈';
        });
    </script>
</body>
</html>