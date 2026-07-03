<?php
session_start();
require_once 'config.php';

// Si ya está logueado, redirigir a admin
if (isset($_SESSION['admin_logged']) && $_SESSION['admin_logged'] === true) {
    header('Location: admin.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    // La contraseña se almacena en una variable de entorno o aquí (hasheada)
    // Por defecto, la contraseña es "admin123" (puedes cambiarla)
    $hash = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'; // hash de "admin123"
    if (password_verify($password, $hash)) {
        $_SESSION['admin_logged'] = true;
        header('Location: admin.php');
        exit;
    } else {
        $error = 'Contraseña incorrecta.';
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
        body {
            font-family: 'Inter', sans-serif;
            background: #0b132b;
            color: #f8fafc;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
        .login-box {
            background: #1c2541;
            padding: 2.5rem;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
            width: 100%;
            max-width: 400px;
        }
        .login-box h1 {
            color: #00f5d4;
            text-align: center;
            margin-bottom: 1.5rem;
        }
        .login-box input {
            width: 100%;
            padding: 0.8rem 1rem;
            margin-bottom: 1rem;
            border: 2px solid transparent;
            border-radius: 8px;
            background: #0b132b;
            color: #fff;
            font-family: 'Inter', sans-serif;
            font-size: 1rem;
            transition: border 0.3s;
        }
        .login-box input:focus {
            outline: none;
            border-color: #00f5d4;
        }
        .login-box button {
            width: 100%;
            padding: 0.8rem;
            background: #00f5d4;
            color: #0b132b;
            border: none;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.3s;
        }
        .login-box button:hover {
            background: #00d4b8;
        }
        .error {
            color: #ff6b6b;
            text-align: center;
            margin-bottom: 1rem;
        }
        .login-box p {
            text-align: center;
            color: #b0b8d1;
            font-size: 0.9rem;
            margin-top: 1rem;
        }
        .login-box a {
            color: #00f5d4;
        }
    </style>
</head>
<body>
    <div class="login-box">
        <h1>🔐 Acceso Admin</h1>
        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="post">
            <input type="password" name="password" placeholder="Contraseña" required />
            <button type="submit">Ingresar</button>
        </form>
        <p>Contraseña por defecto: <strong>admin123</strong></p>
    </div>
</body>
</html>