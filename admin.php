<?php
session_start();
require_once 'funciones.php';

if (!isset($_SESSION['admin_logged']) || $_SESSION['admin_logged'] !== true) {
    header('Location: login.php');
    exit;
}

$mensaje = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Error de seguridad. Intenta nuevamente.';
    } else {
        if ($_POST['action'] === 'update') {
            $seccion = $_POST['seccion'] ?? '';
            $clave = $_POST['clave'] ?? '';
            $valor = $_POST['valor'] ?? '';
            if ($seccion && $clave) {
                if (updateContent($seccion, $clave, $valor)) {
                    $mensaje = "✅ Dato actualizado correctamente.";
                } else {
                    $error = "❌ Error al actualizar.";
                }
            }
        } elseif ($_POST['action'] === 'upload_image') {
            $seccion = $_POST['seccion'] ?? '';
            $clave = $_POST['clave'] ?? '';
            if ($seccion && $clave && isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
                $result = uploadToSupabase($_FILES['imagen']);
                if (isset($result['error'])) {
                    $error = "❌ " . $result['error'];
                } else {
                    $publicUrl = $result['success'];
                    if (updateContent($seccion, $clave, $publicUrl)) {
                        $mensaje = "✅ Imagen subida a Supabase Storage y guardada en la base de datos.";
                    } else {
                        $error = "❌ Error al guardar la URL en la base de datos.";
                    }
                }
            } else {
                $error = "❌ No se seleccionó ninguna imagen o hubo un error.";
            }
        }
    }
}

$csrf_token = generateCSRFToken();
$contenido = getAllContent();
$totalCampos = 0;
foreach ($contenido as $seccion => $datos) {
    $totalCampos += count($datos);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrador GEINFTEC</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; background: #0b132b; color: #f8fafc; padding: 2rem; }
        .container { max-width: 1200px; margin: 0 auto; }
        .header-admin { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; margin-bottom: 2rem; }
        h1 { color: #00f5d4; font-size: 2rem; }
        .badge { background: #1c2541; padding: 0.3rem 1rem; border-radius: 50px; font-size: 0.9rem; color: #b0b8d1; }
        .admin-actions { display: flex; gap: 1rem; align-items: center; }
        .logout { background: #ff6b6b; padding: 0.5rem 1.2rem; border-radius: 50px; color: #fff; text-decoration: none; font-weight: 600; transition: background 0.3s; }
        .logout:hover { background: #e55a5a; }
        .btn-sitio { background: #00f5d4; padding: 0.5rem 1.2rem; border-radius: 50px; color: #0b132b; text-decoration: none; font-weight: 600; transition: background 0.3s; }
        .btn-sitio:hover { background: #00d4b8; }
        .bienvenida { background: #1c2541; padding: 1.5rem; border-radius: 12px; margin-bottom: 2rem; border-left: 4px solid #00f5d4; }
        .bienvenida h2 { color: #00f5d4; margin-bottom: 0.5rem; }
        .bienvenida p { color: #b0b8d1; }
        .seccion { background: #1c2541; padding: 1.5rem; border-radius: 12px; margin-bottom: 2rem; border: 1px solid rgba(0,245,212,0.1); }
        .seccion h2 { color: #00f5d4; border-bottom: 1px solid rgba(0,245,212,0.2); padding-bottom: 0.5rem; margin-bottom: 1rem; display: flex; justify-content: space-between; align-items: center; }
        .seccion h2 small { font-weight: 400; font-size: 0.8rem; color: #b0b8d1; }
        .campo { display: flex; flex-wrap: wrap; gap: 0.8rem; align-items: center; margin: 0.8rem 0; border-bottom: 1px solid rgba(255,255,255,0.05); padding-bottom: 0.8rem; }
        .campo label { min-width: 120px; font-weight: 600; color: #b0b8d1; font-size: 0.9rem; }
        .campo input, .campo textarea { flex: 1; padding: 0.6rem 0.8rem; border-radius: 8px; border: 1px solid #2a3a5e; background: #0b132b; color: #fff; font-family: 'Inter', sans-serif; font-size: 0.95rem; transition: border 0.3s; min-width: 150px; }
        .campo input:focus, .campo textarea:focus { outline: none; border-color: #00f5d4; }
        .campo textarea { min-height: 60px; resize: vertical; }
        .btn { background: #00f5d4; color: #0b132b; padding: 0.4rem 1.2rem; border: none; border-radius: 50px; cursor: pointer; font-weight: 600; transition: background 0.3s; }
        .btn:hover { background: #00d4b8; }
        .btn-edit { background: #7209b7; color: #fff; }
        .btn-edit:hover { background: #5a0793; }
        .mensaje { padding: 0.8rem 1.2rem; border-radius: 8px; margin-bottom: 1rem; font-weight: 600; }
        .mensaje.success { background: rgba(0,245,212,0.15); color: #00f5d4; border: 1px solid #00f5d4; }
        .mensaje.error { background: rgba(255,107,107,0.15); color: #ff6b6b; border: 1px solid #ff6b6b; }
        .imagen-preview { max-width: 100px; max-height: 80px; border-radius: 6px; object-fit: cover; margin-right: 0.5rem; border: 1px solid #2a3a5e; }
        .subir-imagen { display: flex; flex-wrap: wrap; gap: 0.5rem; align-items: center; }
        .subir-imagen input[type="file"] { color: #b0b8d1; font-size: 0.85rem; }
        .campo-valor { display: flex; flex-wrap: wrap; gap: 0.5rem; flex: 1; align-items: center; }
        .campo-valor .texto-actual { color: #b0b8d1; font-size: 0.85rem; word-break: break-all; }
        .empty { color: #666; font-style: italic; }
        .recomendacion { background: rgba(0,245,212,0.08); padding: 0.5rem 1rem; border-radius: 8px; border-left: 3px solid #00f5d4; margin: 0.5rem 0; }
        .recomendacion strong { color: #00f5d4; }
        @media (max-width: 768px) {
            .campo label { min-width: 100%; }
            .header-admin { flex-direction: column; align-items: stretch; }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header-admin">
        <div>
            <h1>⚙️ Panel de Administración</h1>
            <span class="badge"><?php echo $totalCampos; ?> campos editables</span>
        </div>
        <div class="admin-actions">
            <a href="index.php" target="_blank" class="btn-sitio">🌐 Ver sitio</a>
            <a href="logout.php" class="logout">🚪 Cerrar sesión</a>
        </div>
    </div>

    <div class="bienvenida">
        <h2>👋 ¡Hola, administrador!</h2>
        <p>
            Desde aquí puedes editar <strong>todos los textos, títulos, estadísticas e imágenes</strong> del sitio.
            Las imágenes se suben a <strong>Supabase Storage</strong> y son persistentes. <br>
            <span style="color:#00f5d4;">✅ Los cambios se reflejan al instante en la página pública.</span>
        </p>
    </div>

    <?php if ($mensaje): ?>
        <div class="mensaje success"><?php echo htmlspecialchars($mensaje); ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="mensaje error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <?php foreach ($contenido as $seccion => $datos): ?>
        <div class="seccion">
            <h2>
                <?php echo htmlspecialchars(ucfirst($seccion)); ?>
                <small><?php echo count($datos); ?> campos</small>
            </h2>
            <?php foreach ($datos as $clave => $valor): ?>
                <div class="campo">
                    <label for="<?php echo $seccion.'_'.$clave; ?>"><?php echo htmlspecialchars($clave); ?></label>
                    <div class="campo-valor">
                        <?php if (strpos($clave, 'imagen') !== false || strpos($clave, 'img') !== false || strpos($clave, 'foto') !== false): ?>
                            <div class="subir-imagen">
                                <?php if ($valor && filter_var($valor, FILTER_VALIDATE_URL)): ?>
                                    <img src="<?php echo htmlspecialchars($valor); ?>" alt="preview" class="imagen-preview">
                                <?php endif; ?>
                                <form method="post" enctype="multipart/form-data" style="display:inline-flex; gap:0.5rem; align-items:center; flex-wrap:wrap;">
                                    <input type="hidden" name="action" value="upload_image">
                                    <input type="hidden" name="seccion" value="<?php echo htmlspecialchars($seccion); ?>">
                                    <input type="hidden" name="clave" value="<?php echo htmlspecialchars($clave); ?>">
                                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                                    <input type="file" name="imagen" accept="image/*" required>
                                    <button type="submit" class="btn">Subir a Supabase</button>
                                </form>
                                <form method="post" style="display:inline-flex; gap:0.5rem; align-items:center; flex-wrap:wrap;">
                                    <input type="hidden" name="action" value="update">
                                    <input type="hidden" name="seccion" value="<?php echo htmlspecialchars($seccion); ?>">
                                    <input type="hidden" name="clave" value="<?php echo htmlspecialchars($clave); ?>">
                                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                                    <input type="text" name="valor" value="<?php echo htmlspecialchars($valor); ?>" placeholder="URL de imagen" style="flex:1; min-width:120px;">
                                    <button type="submit" class="btn btn-edit">Actualizar</button>
                                </form>
                            </div>
                        <?php else: ?>
                            <form method="post" style="display:flex; gap:0.5rem; width:100%; flex-wrap:wrap;">
                                <input type="hidden" name="action" value="update">
                                <input type="hidden" name="seccion" value="<?php echo htmlspecialchars($seccion); ?>">
                                <input type="hidden" name="clave" value="<?php echo htmlspecialchars($clave); ?>">
                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                                <?php if (strlen($valor) > 100): ?>
                                    <textarea name="valor" style="flex:1; min-width:200px;"><?php echo htmlspecialchars($valor); ?></textarea>
                                <?php else: ?>
                                    <input type="text" name="valor" value="<?php echo htmlspecialchars($valor); ?>" style="flex:1; min-width:200px;">
                                <?php endif; ?>
                                <button type="submit" class="btn btn-edit">Actualizar</button>
                            </form>
                        <?php endif; ?>
                        <?php if ($valor && !empty($valor)): ?>
                            <span class="texto-actual">(actual: <?php echo htmlspecialchars(substr($valor, 0, 50)) . (strlen($valor) > 50 ? '…' : ''); ?>)</span>
                        <?php else: ?>
                            <span class="texto-actual empty">(vacío)</span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
            <div class="recomendacion">
                💡 <strong>Tip:</strong> Las imágenes se suben a Supabase Storage y se guarda la URL pública. Puedes usar cualquier imagen de internet pegando su URL.
            </div>
        </div>
    <?php endforeach; ?>
</div>
</body>
</html>