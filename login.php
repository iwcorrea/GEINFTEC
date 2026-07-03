<?php
session_start();
require_once 'config.php';

// Si ya está logueado, redirigir a admin
if (isset($_SESSION['admin_logged']) && $_SESSION['admin_logged'] === true) {
    header('Location: admin.php');
    exit;
}

$error = '';
$attempts = 0; // Podríamos añadir control de intentos, pero lo dejamos simple

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    $admin_pass = getenv('ADMIN_PASSWORD') ?: 'admin123';
    
    // Simular un pequeño retardo para evitar ataques de fuerza bruta (opcional)
    sleep(1);
    
    if ($password === $admin_pass) {
        $_SESSION['admin_logged'] = true;
        // Redirigir a admin.php
        header('Location: admin.php');
        exit;
    } else {
        $error = '❌ Contraseña incorrecta. Intenta de nuevo.';
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
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', sans-serif;
            background: #0b132b;
            color: #f8fafc;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 1rem;
        }
        .login-box {
            background: #1c2541;
            padding: 2.5rem 2rem;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.6);
            width: 100%;
            max-width: 420px;
            border: 1px solid rgba(0,245,212,0.15);
            position: relative;
        }
        .login-box .logo-icon {
            text-align: center;
            font-size: 3rem;
            margin-bottom: 0.25rem;
        }
        .login-box h1 {
            color: #00f5d4;
            text-align: center;
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }
        .login-box .sub {
            text-align: center;
            color: #b0b8d1;
            margin-bottom: 1.8rem;
            font-size: 0.95rem;
        }
        .login-box .back-link {
            display: inline-block;
            color: #b0b8d1;
            text-decoration: none;
            font-size: 0.9rem;
            margin-bottom: 1.5rem;
            transition: color 0.3s;
        }
        .login-box .back-link:hover {
            color: #00f5d4;
        }
        .login-box .back-link::before {
            content: '← ';
        }
        .login-box input[type="password"] {
            width: 100%;
            padding: 0.9rem 1rem;
            margin-bottom: 1.2rem;
            border: 2px solid transparent;
            border-radius: 10px;
            background: #0b132b;
            color: #fff;
            font-family: 'Inter', sans-serif;
            font-size: 1rem;
            transition: border 0.3s, box-shadow 0.3s, background 0.3s;
        }
        .login-box input[type="password"]:focus {
            outline: none;
            border-color: #00f5d4;
            box-shadow: 0 0 20px rgba(0,245,212,0.15);
            background: #0f1a35;
        }
        .login-box input[type="password"]:hover {
            background: #0f1a35;
        }
        .login-box button {
            width: 100%;
            padding: 0.9rem;
            background: #00f5d4;
            color: #0b132b;
            border: none;
            border-radius: 50px;
            font-weight: 700;
            font-size: 1.05rem;
            cursor: pointer;
            transition: background 0.3s, transform 0.2s, box-shadow 0.3s;
            margin-top: 0.2rem;
        }
        .login-box button:hover {
            background: #00d4b8;
            transform: scale(1.02);
            box-shadow: 0 0 20px rgba(0,245,212,0.3);
        }
        .login-box button:active {
            transform: scale(0.98);
        }
        .error {
            color: #ff6b6b;
            text-align: center;
            margin-bottom: 1rem;
            padding: 0.6rem;
            background: rgba(255,107,107,0.1);
            border-radius: 8px;
            font-weight: 600;
            border-left: 3px solid #ff6b6b;
            animation: fadeIn 0.3s ease;
        }
        .login-box .footer-text {
            text-align: center;
            color: #b0b8d1;
            font-size: 0.8rem;
            margin-top: 1.5rem;
            border-top: 1px solid rgba(255,255,255,0.05);
            padding-top: 1.2rem;
        }
        .login-box .footer-text strong {
            color: #00f5d4;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-5px); }
            to { opacity: 1; transform: translateY(0); }
        }
        /* Estilo para el campo cuando hay error */
        .input-error {
            border-color: #ff6b6b !important;
            box-shadow: 0 0 15px rgba(255,107,107,0.2) !important;
        }
        .lock-icon {
            font-size: 2.5rem;
        }
        @media (max-width: 480px) {
            .login-box { padding: 2rem 1.2rem; }
        }
    </style>
</head>
<body>

<div class="login-box">
    <div class="logo-icon">🔐</div>
    <h1>GEINFTEC</h1>
    <p class="sub">Acceso al panel de administración</p>

    <!-- Enlace para volver al inicio -->
    <a href="index.php" class="back-link">Volver al sitio</a>

    <?php if ($error): ?>
        <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="post" id="loginForm" autocomplete="off">
        <input type="password" id="password" name="password" placeholder="Contraseña" required autofocus />
        <button type="submit" id="loginBtn">Ingresar</button>
    </form>

    <div class="footer-text">
        Contraseña definida en <strong>ADMIN_PASSWORD</strong>
        <br>
        <span style="color:#666; font-size:0.75rem;">(o usa el fallback por defecto)</span>
    </div>
</div>

<script>
    // Pequeño script para mejorar la experiencia:
    // - Si hay error, resaltar el campo.
    // - Quitar el error al escribir.
    (function() {
        const passwordInput = document.getElementById('password');
        const errorDiv = document.querySelector('.error');
        const loginForm = document.getElementById('loginForm');

        // Si hay un error, añadir clase al campo
        if (errorDiv) {
            passwordInput.classList.add('input-error');
            // Al empezar a escribir, quitar el error visual y el mensaje
            passwordInput.addEventListener('input', function() {
                this.classList.remove('input-error');
                if (errorDiv) {
                    errorDiv.style.display = 'none';
                }
            });
        }

        // Prevenir envíos duplicados (opcional)
        loginForm.addEventListener('submit', function() {
            const btn = document.getElementById('loginBtn');
            btn.disabled = true;
            btn.textContent = 'Verificando...';
            // Si la contraseña es incorrecta, el servidor devuelve la página con error,
            // así que el botón se re-habilita al recargar.
            // Si queremos reintentar, lo dejamos así.
        });
    })();
</script>
</body>
</html>