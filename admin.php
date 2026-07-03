<?php
session_start();
require_once 'funciones.php';

// Verificar autenticación
if (!isset($_SESSION['admin_logged']) || $_SESSION['admin_logged'] !== true) {
    header('Location: login.php');
    exit;
}

$mensaje = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'update') {
        $seccion = $_POST['seccion'] ?? '';
        $clave = $_POST['clave'] ?? '';
        $valor = $_POST['valor'] ?? '';
        if ($seccion && $clave) {
            if (updateContent($seccion, $clave, $valor)) {
                $mensaje = "Dato actualizado correctamente.";
            } else {
                $error = "Error al actualizar.";
            }
        }
    } elseif ($_POST['action'] === 'upload_image') {
        $seccion = $_POST['seccion'] ?? '';
        $clave = $_POST['clave'] ?? '';
        if ($seccion && $clave && isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = 'uploads/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
            $ext = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
            $filename = uniqid() . '.' . $ext;
            $destino = $uploadDir . $filename;
            if (move_uploaded_file($_FILES['imagen']['tmp_name'], $destino)) {
                if (updateContent($seccion, $clave, $destino)) {
                    $mensaje = "Imagen subida y guardada.";
                } else {
                    $error = "Error al guardar la ruta.";
                }
            } else {
                $error = "Error al mover el archivo.";
            }
        } else {
            $error = "No se seleccionó ninguna imagen o hubo un error.";
        }
    }
}

$contenido = getAllContent();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrador GEINFTEC</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background: #0b132b; color: #f8fafc; padding: 2rem; }
        .container { max-width: 1200px; margin: 0 auto; }
        h1 { color: #00f5d4; }
        .seccion { background: #1c2541; padding: 1.5rem; border-radius: 12px; margin-bottom: 2rem; }
        .seccion h2 { color: #00f5d4; border-bottom: 1px solid #00f5d4; padding-bottom: 0.5rem; }
        .campo { display: flex; flex-wrap: wrap; gap: 1rem; align-items: center; margin: 1rem 0; border-bottom: 1px solid #2a3a5e; padding-bottom: 1rem; }
        .campo label { min-width: 120px; font-weight: 600; }
        .campo input, .campo textarea { flex: 1; padding: 0.5rem; border-radius: 6px; border: 1px solid #2a3a5e; background: #0b132b; color: #fff; }
        .campo textarea { min-height: 60px; }
        .btn { background: #00f5d4; color: #0b132b; padding: 0.5rem 1.5rem; border: none; border-radius: 50px; cursor: pointer; font-weight: 600; }
        .btn:hover { background: #00d4b8; }
        .mensaje { padding: 1rem; border-radius: 8px; margin-bottom: 1rem; }
        .mensaje.success { background: #00f5d4; color: #0b132b; }
        .mensaje.error { background: #ff6b6b; color: #fff; }
        .imagen-preview { max-width: 150px; max-height: 150px; border-radius: 8px; margin-right: 1rem; }
        .subir-imagen { display: flex; gap: 1rem; align-items: center; flex-wrap: wrap; }
        .subir-imagen input[type="file"] { color: #fff; }
        .volver { display: inline-block; margin-top: 2rem; color: #00f5d4; }
        .logout { float: right; background: #ff6b6b; padding: 0.5rem 1rem; border-radius: 50px; color: #fff; text-decoration: none; }
        .logout:hover { background: #e55a5a; }
    </style>
</head>
<body>
<div class="container">
    <h1>Panel de Administración - GEINFTEC <a href="logout.php" class="logout">Cerrar sesión</a></h1>
    <p><a href="index.php" target="_blank" class="volver">← Ver sitio</a></p>

    <?php if ($mensaje): ?>
        <div class="mensaje success"><?php echo $mensaje; ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="mensaje error"><?php echo $error; ?></div>
    <?php endif; ?>

    <?php foreach ($contenido as $seccion => $datos): ?>
        <div class="seccion">
            <h2><?php echo ucfirst($seccion); ?></h2>
            <?php foreach ($datos as $clave => $valor): ?>
                <div class="campo">
                    <label for="<?php echo $seccion.'_'.$clave; ?>"><?php echo $clave; ?></label>
                    <?php if (strpos($clave, 'imagen') !== false || strpos($clave, 'img') !== false || strpos($clave, 'foto') !== false): ?>
                        <div class="subir-imagen">
                            <?php if ($valor && file_exists($valor)): ?>
                                <img src="<?php echo $valor; ?>" alt="preview" class="imagen-preview">
                            <?php endif; ?>
                            <form method="post" enctype="multipart/form-data" style="display:inline;">
                                <input type="hidden" name="action" value="upload_image">
                                <input type="hidden" name="seccion" value="<?php echo $seccion; ?>">
                                <input type="hidden" name="clave" value="<?php echo $clave; ?>">
                                <input type="file" name="imagen" accept="image/*" required>
                                <button type="submit" class="btn">Subir imagen</button>
                            </form>
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="action" value="update">
                                <input type="hidden" name="seccion" value="<?php echo $seccion; ?>">
                                <input type="hidden" name="clave" value="<?php echo $clave; ?>">
                                <input type="text" name="valor" value="<?php echo htmlspecialchars($valor); ?>" style="flex:1; min-width:200px;">
                                <button type="submit" class="btn">Actualizar</button>
                            </form>
                        </div>
                    <?php else: ?>
                        <form method="post" style="display:flex; gap:0.5rem; width:100%; flex-wrap:wrap;">
                            <input type="hidden" name="action" value="update">
                            <input type="hidden" name="seccion" value="<?php echo $seccion; ?>">
                            <input type="hidden" name="clave" value="<?php echo $clave; ?>">
                            <?php if (strlen($valor) > 100): ?>
                                <textarea name="valor" style="flex:1; min-width:200px;"><?php echo htmlspecialchars($valor); ?></textarea>
                            <?php else: ?>
                                <input type="text" name="valor" value="<?php echo htmlspecialchars($valor); ?>" style="flex:1; min-width:200px;">
                            <?php endif; ?>
                            <button type="submit" class="btn">Actualizar</button>
                        </form>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>
</div>
</body>
</html>