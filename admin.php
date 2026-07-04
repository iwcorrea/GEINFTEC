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

// Procesar acciones vía AJAX (sin recargar)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        echo json_encode(['success' => false, 'error' => 'Error de seguridad.']);
        exit;
    }
    if ($_POST['action'] === 'update') {
        $seccion = $_POST['seccion'] ?? '';
        $clave = $_POST['clave'] ?? '';
        $valor = $_POST['valor'] ?? '';
        if ($seccion && $clave) {
            if (updateContent($seccion, $clave, $valor)) {
                echo json_encode(['success' => true, 'message' => '✅ Actualizado correctamente.']);
            } else {
                echo json_encode(['success' => false, 'error' => '❌ Error al actualizar.']);
            }
        } else {
            echo json_encode(['success' => false, 'error' => '❌ Datos incompletos.']);
        }
        exit;
    } elseif ($_POST['action'] === 'upload_image') {
        $seccion = $_POST['seccion'] ?? '';
        $clave = $_POST['clave'] ?? '';
        if ($seccion && $clave && isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
            $result = uploadToSupabase($_FILES['imagen']);
            if (isset($result['error'])) {
                echo json_encode(['success' => false, 'error' => '❌ ' . $result['error']]);
            } else {
                $publicUrl = $result['success'];
                if (updateContent($seccion, $clave, $publicUrl)) {
                    echo json_encode(['success' => true, 'message' => '✅ Imagen subida y guardada.', 'url' => $publicUrl]);
                } else {
                    echo json_encode(['success' => false, 'error' => '❌ Error al guardar la URL.']);
                }
            }
        } else {
            echo json_encode(['success' => false, 'error' => '❌ No se seleccionó ninguna imagen.']);
        }
        exit;
    }
}

$contenido = getAllContent();
$imagenes = listImagesFromBucket();
$csrf_token = generateCSRFToken();

// ============================================================
// MEJORA: Filtrar solo las claves de imágenes de items (itemX_img)
// ============================================================
function filtrarClaves($campos, $seccion) {
    if ($seccion === 'proyectos' || $seccion === 'equipo') {
        // Solo mantener claves que empiecen con 'item' y terminen con '_img'
        $filtradas = [];
        foreach ($campos as $clave => $valor) {
            if (preg_match('/^item\d+_img$/', $clave)) {
                $filtradas[$clave] = $valor;
            }
        }
        // También mantener otras claves que no sean imágenes (como títulos, descripciones)
        foreach ($campos as $clave => $valor) {
            if (!preg_match('/^item\d+_img$/', $clave) && strpos($clave, 'img') === false && strpos($clave, 'imagen') === false && strpos($clave, 'foto') === false) {
                $filtradas[$clave] = $valor;
            }
        }
        return $filtradas;
    }
    return $campos;
}

foreach ($contenido as $seccion => &$campos) {
    $campos = filtrarClaves($campos, $seccion);
}
unset($campos);

// ============================================================
// MEJORA: Ordenar campos para que las imágenes aparezcan primero
// ============================================================
function ordenarCampos($campos) {
    $imagenes = [];
    $otros = [];
    foreach ($campos as $clave => $valor) {
        if (strpos($clave, 'img') !== false || strpos($clave, 'imagen') !== false || strpos($clave, 'foto') !== false) {
            $imagenes[$clave] = $valor;
        } else {
            $otros[$clave] = $valor;
        }
    }
    return array_merge($imagenes, $otros);
}

foreach ($contenido as $seccion => &$campos) {
    $campos = ordenarCampos($campos);
}
unset($campos);

$secciones = array_keys($contenido);
$primeraSeccion = $secciones[0] ?? 'hero';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - GEINFTEC</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; background: #0b132b; color: #f8fafc; padding: 1.5rem; }
        .container { max-width: 1400px; margin: 0 auto; }
        .header-admin { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; margin-bottom: 2rem; }
        h1 { color: #00f5d4; font-size: 1.8rem; }
        .badge { background: #1c2541; padding: 0.3rem 1rem; border-radius: 50px; font-size: 0.9rem; color: #b0b8d1; }
        .admin-actions { display: flex; gap: 1rem; align-items: center; flex-wrap: wrap; }
        .btn { background: #00f5d4; color: #0b132b; padding: 0.5rem 1.2rem; border: none; border-radius: 50px; cursor: pointer; font-weight: 600; transition: background 0.3s; text-decoration: none; display: inline-block; }
        .btn:hover { background: #00d4b8; }
        .btn-danger { background: #ff6b6b; color: #fff; }
        .btn-danger:hover { background: #e55a5a; }
        .btn-outline { background: transparent; border: 2px solid #00f5d4; color: #00f5d4; }
        .btn-outline:hover { background: #00f5d4; color: #0b132b; }
        .btn-sm { padding: 0.3rem 0.8rem; font-size: 0.8rem; }
        .tabs { display: flex; flex-wrap: wrap; gap: 0.5rem; margin-bottom: 2rem; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 0.5rem; }
        .tab { padding: 0.5rem 1.2rem; border-radius: 50px; cursor: pointer; background: #1c2541; color: #b0b8d1; transition: all 0.3s; font-weight: 600; }
        .tab.active { background: #00f5d4; color: #0b132b; }
        .tab:hover { background: #2a3a5e; color: #fff; }
        .tab-content { display: none; }
        .tab-content.active { display: block; }
        .seccion-card { background: #1c2541; padding: 1.5rem; border-radius: 12px; margin-bottom: 2rem; border: 1px solid rgba(0,245,212,0.1); }
        .seccion-card h2 { color: #00f5d4; border-bottom: 1px solid rgba(0,245,212,0.2); padding-bottom: 0.5rem; margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center; }
        .seccion-card h2 small { font-weight: 400; font-size: 0.8rem; color: #b0b8d1; }
        .campo { display: flex; flex-wrap: wrap; gap: 1rem; align-items: flex-start; margin-bottom: 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.05); padding-bottom: 1.5rem; }
        .campo label { min-width: 150px; font-weight: 600; color: #b0b8d1; font-size: 0.9rem; padding-top: 0.5rem; }
        .campo .valor { flex: 1; min-width: 250px; }
        .campo input, .campo textarea { width: 100%; padding: 0.6rem 0.8rem; border-radius: 8px; border: 1px solid #2a3a5e; background: #0b132b; color: #fff; font-family: 'Inter', sans-serif; font-size: 0.95rem; transition: border 0.3s; }
        .campo input:focus, .campo textarea:focus { outline: none; border-color: #00f5d4; }
        .campo textarea { min-height: 80px; resize: vertical; }
        .campo .acciones { display: flex; flex-wrap: wrap; gap: 0.5rem; align-items: center; margin-top: 0.5rem; }
        .campo .preview-img { max-width: 120px; max-height: 100px; border-radius: 8px; object-fit: cover; border: 1px solid #2a3a5e; margin: 0.5rem 0; }
        .campo .img-container { display: flex; align-items: center; gap: 1rem; flex-wrap: wrap; }
        .campo .status { font-size: 0.85rem; color: #b0b8d1; margin-top: 0.3rem; }
        .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 1000; justify-content: center; align-items: center; }
        .modal.active { display: flex; }
        .modal-content { background: #1c2541; padding: 2rem; border-radius: 16px; max-width: 800px; width: 90%; max-height: 80vh; overflow-y: auto; }
        .modal-content h3 { color: #00f5d4; margin-bottom: 1rem; }
        .modal-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 1rem; }
        .modal-item { background: #0b132b; border-radius: 8px; padding: 0.5rem; text-align: center; cursor: pointer; transition: all 0.3s; border: 2px solid transparent; }
        .modal-item:hover { border-color: #00f5d4; transform: scale(1.02); }
        .modal-item img { width: 100%; height: 120px; object-fit: cover; border-radius: 6px; }
        .modal-item .name { font-size: 0.7rem; color: #b0b8d1; margin-top: 0.3rem; word-break: break-all; }
        .modal-close { float: right; background: none; border: none; color: #fff; font-size: 1.5rem; cursor: pointer; }
        .mensaje { padding: 0.8rem 1.2rem; border-radius: 8px; margin-bottom: 1rem; font-weight: 600; }
        .mensaje.success { background: rgba(0,245,212,0.15); color: #00f5d4; border: 1px solid #00f5d4; }
        .mensaje.error { background: rgba(255,107,107,0.15); color: #ff6b6b; border: 1px solid #ff6b6b; }
        .mensaje { display: none; }
        .mensaje.show { display: block; }
        .help-text { color: #b0b8d1; font-size: 0.8rem; margin-top: 0.3rem; }
        .btn-refresh { background: transparent; color: #00f5d4; border: 1px solid #00f5d4; padding: 0.2rem 0.8rem; border-radius: 50px; cursor: pointer; font-size: 0.8rem; }
        .btn-refresh:hover { background: #00f5d4; color: #0b132b; }
        @media (max-width: 768px) {
            .campo label { min-width: 100%; }
            .header-admin { flex-direction: column; align-items: stretch; }
            .tabs { justify-content: center; }
        }
    </style>
</head>
<body>
<div class="container">
    <!-- Header -->
    <div class="header-admin">
        <div>
            <h1>⚙️ Panel de Administración</h1>
            <span class="badge"><?php echo count($contenido); ?> secciones</span>
        </div>
        <div class="admin-actions">
            <a href="index.php" target="_blank" class="btn">🌐 Ver sitio</a>
            <a href="logout.php" class="btn btn-danger">🚪 Cerrar sesión</a>
        </div>
    </div>

    <!-- Mensajes -->
    <div id="mensajeGlobal" class="mensaje"></div>

    <!-- Tabs -->
    <div class="tabs" id="tabsContainer">
        <?php foreach ($secciones as $index => $seccion): ?>
            <div class="tab <?php echo $index === 0 ? 'active' : ''; ?>" data-tab="<?php echo $seccion; ?>">
                <?php echo ucfirst($seccion); ?>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Contenido de Tabs -->
    <?php foreach ($secciones as $index => $seccion): ?>
        <div class="tab-content <?php echo $index === 0 ? 'active' : ''; ?>" id="tab-<?php echo $seccion; ?>">
            <div class="seccion-card">
                <h2>
                    <?php echo ucfirst($seccion); ?>
                    <small><?php echo count($contenido[$seccion]); ?> campos</small>
                </h2>
                <?php foreach ($contenido[$seccion] as $clave => $valor): ?>
                    <div class="campo" data-seccion="<?php echo $seccion; ?>" data-clave="<?php echo $clave; ?>">
                        <label for="<?php echo $seccion.'_'.$clave; ?>">
                            <?php 
                            $label = $clave;
                            if (strpos($clave, 'img') !== false || strpos($clave, 'imagen') !== false || strpos($clave, 'foto') !== false) {
                                $label = '🖼️ ' . $clave;
                            }
                            echo htmlspecialchars($label); 
                            ?>
                        </label>
                        <div class="valor">
                            <?php if (strpos($clave, 'imagen') !== false || strpos($clave, 'img') !== false || strpos($clave, 'foto') !== false): ?>
                                <div class="img-container">
                                    <?php if ($valor && filter_var($valor, FILTER_VALIDATE_URL)): ?>
                                        <img src="<?php echo htmlspecialchars($valor); ?>" alt="preview" class="preview-img">
                                    <?php else: ?>
                                        <div style="color:#666; font-size:0.8rem;">Sin imagen</div>
                                    <?php endif; ?>
                                    <div>
                                        <form class="ajax-form" enctype="multipart/form-data" method="post" action="">
                                            <input type="hidden" name="action" value="upload_image">
                                            <input type="hidden" name="seccion" value="<?php echo htmlspecialchars($seccion); ?>">
                                            <input type="hidden" name="clave" value="<?php echo htmlspecialchars($clave); ?>">
                                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                                            <input type="file" name="imagen" accept="image/*" required style="margin-bottom:0.5rem; display:block;">
                                            <button type="submit" class="btn btn-sm">Subir nueva</button>
                                        </form>
                                        <button type="button" class="btn btn-outline btn-sm" onclick="openImageSelector('<?php echo $seccion; ?>', '<?php echo $clave; ?>')">Elegir existente</button>
                                    </div>
                                </div>
                                <form class="ajax-form" method="post" style="margin-top:0.5rem;">
                                    <input type="hidden" name="action" value="update">
                                    <input type="hidden" name="seccion" value="<?php echo htmlspecialchars($seccion); ?>">
                                    <input type="hidden" name="clave" value="<?php echo htmlspecialchars($clave); ?>">
                                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                                    <input type="text" name="valor" value="<?php echo htmlspecialchars($valor); ?>" placeholder="URL de imagen" style="width:100%;">
                                    <button type="submit" class="btn btn-sm" style="margin-top:0.3rem;">Actualizar URL</button>
                                </form>
                            <?php else: ?>
                                <form class="ajax-form" method="post">
                                    <input type="hidden" name="action" value="update">
                                    <input type="hidden" name="seccion" value="<?php echo htmlspecialchars($seccion); ?>">
                                    <input type="hidden" name="clave" value="<?php echo htmlspecialchars($clave); ?>">
                                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                                    <?php if (strlen($valor) > 100): ?>
                                        <textarea name="valor"><?php echo htmlspecialchars($valor); ?></textarea>
                                    <?php else: ?>
                                        <input type="text" name="valor" value="<?php echo htmlspecialchars($valor); ?>">
                                    <?php endif; ?>
                                    <button type="submit" class="btn btn-sm" style="margin-top:0.3rem;">Actualizar</button>
                                </form>
                            <?php endif; ?>
                            <div class="status"></div>
                            <?php if ($valor && !empty($valor) && strlen($valor) < 100): ?>
                                <div class="help-text">Valor: <?php echo htmlspecialchars(substr($valor, 0, 80)); ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
                <div style="margin-top:1rem; padding:0.8rem; background:rgba(0,245,212,0.05); border-radius:8px; border-left:3px solid #00f5d4;">
                    <p style="color:#b0b8d1; font-size:0.85rem;">
                        💡 <strong>Consejo:</strong> Para imágenes, usa el botón "Elegir existente" para reutilizar imágenes ya subidas.
                        También puedes pegar la URL de cualquier imagen de internet.
                    </p>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Modal para selector de imágenes -->
<div class="modal" id="imageModal">
    <div class="modal-content">
        <button class="modal-close" onclick="closeImageSelector()">&times;</button>
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem;">
            <h3>Seleccionar imagen existente</h3>
            <button class="btn btn-sm btn-refresh" onclick="refreshImages()">🔄 Refrescar</button>
        </div>
        <p style="color:#b0b8d1; margin-bottom:1rem;">Haz clic en una imagen para usarla en este campo.</p>
        <div class="modal-grid" id="imageGrid">
            <?php if (empty($imagenes)): ?>
                <p style="color:#b0b8d1; grid-column: 1/-1; text-align:center; padding:2rem;">
                    No hay imágenes subidas aún. 
                    <br>Sube una imagen usando el botón "Subir nueva" en cualquier campo de imagen.
                    <br><br>
                    <span style="font-size:0.8rem; color:#666;">Si ya subiste imágenes y no aparecen, verifica que el bucket de Supabase esté configurado correctamente.</span>
                </p>
            <?php else: ?>
                <?php foreach ($imagenes as $img): ?>
                    <div class="modal-item" onclick="selectImage('<?php echo htmlspecialchars($img['url']); ?>')">
                        <img src="<?php echo htmlspecialchars($img['url']); ?>" alt="<?php echo htmlspecialchars($img['name']); ?>">
                        <div class="name"><?php echo htmlspecialchars($img['name']); ?></div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    // ============================================================
    // 1. PERSISTENCIA DE PESTAÑAS (sessionStorage)
    // ============================================================
    (function() {
        const activeTab = sessionStorage.getItem('activeTab') || '<?php echo $primeraSeccion; ?>';
        document.querySelectorAll('.tab').forEach(tab => {
            if (tab.dataset.tab === activeTab) {
                tab.classList.add('active');
            } else {
                tab.classList.remove('active');
            }
        });
        document.querySelectorAll('.tab-content').forEach(tc => {
            if (tc.id === 'tab-' + activeTab) {
                tc.classList.add('active');
            } else {
                tc.classList.remove('active');
            }
        });
        document.querySelectorAll('.tab').forEach(tab => {
            tab.addEventListener('click', function() {
                const seccion = this.dataset.tab;
                sessionStorage.setItem('activeTab', seccion);
                document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
                this.classList.add('active');
                document.querySelectorAll('.tab-content').forEach(tc => tc.classList.remove('active'));
                document.getElementById('tab-' + seccion).classList.add('active');
            });
        });
    })();

    // ============================================================
    // 2. ENVÍO ASÍNCRONO (AJAX) DE FORMULARIOS
    // ============================================================
    document.querySelectorAll('.ajax-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const action = formData.get('action');
            const seccion = formData.get('seccion');
            const clave = formData.get('clave');
            const campo = this.closest('.campo');
            const statusDiv = campo.querySelector('.status');

            statusDiv.textContent = '⏳ Procesando...';
            statusDiv.style.color = '#b0b8d1';

            fetch('admin.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    statusDiv.textContent = '✅ ' + data.message;
                    statusDiv.style.color = '#00f5d4';
                    if (action === 'upload_image' && data.url) {
                        const preview = campo.querySelector('.preview-img');
                        if (preview) {
                            preview.src = data.url;
                        } else {
                            const container = campo.querySelector('.img-container');
                            const img = document.createElement('img');
                            img.src = data.url;
                            img.alt = 'preview';
                            img.className = 'preview-img';
                            container.insertBefore(img, container.querySelector('div'));
                        }
                        const urlInput = campo.querySelector('input[name="valor"]');
                        if (urlInput) {
                            urlInput.value = data.url;
                        }
                    }
                    mostrarMensaje('✅ ' + data.message, 'success');
                } else {
                    statusDiv.textContent = '❌ ' + data.error;
                    statusDiv.style.color = '#ff6b6b';
                    mostrarMensaje('❌ ' + data.error, 'error');
                }
            })
            .catch(error => {
                statusDiv.textContent = '❌ Error de conexión.';
                statusDiv.style.color = '#ff6b6b';
                mostrarMensaje('❌ Error de conexión.', 'error');
                console.error('Error:', error);
            });
        });
    });

    // ============================================================
    // 3. MENSAJE GLOBAL
    // ============================================================
    function mostrarMensaje(texto, tipo) {
        const msg = document.getElementById('mensajeGlobal');
        msg.textContent = texto;
        msg.className = 'mensaje show ' + tipo;
        if (window.timeoutMensaje) clearTimeout(window.timeoutMensaje);
        window.timeoutMensaje = setTimeout(() => {
            msg.classList.remove('show');
        }, 5000);
    }

    // ============================================================
    // 4. SELECTOR DE IMÁGENES (MODAL)
    // ============================================================
    let currentSeccion = '';
    let currentClave = '';

    function openImageSelector(seccion, clave) {
        currentSeccion = seccion;
        currentClave = clave;
        document.getElementById('imageModal').classList.add('active');
    }

    function closeImageSelector() {
        document.getElementById('imageModal').classList.remove('active');
    }

    function selectImage(url) {
        closeImageSelector();
        const campos = document.querySelectorAll('.campo');
        for (let campo of campos) {
            const label = campo.querySelector('label');
            if (label && label.textContent.trim().replace('🖼️ ', '') === currentClave) {
                const input = campo.querySelector('input[name="valor"]');
                if (input) {
                    input.value = url;
                    const preview = campo.querySelector('.preview-img');
                    if (preview) {
                        preview.src = url;
                    }
                    mostrarMensaje('✅ Imagen seleccionada. Haz clic en "Actualizar URL" para guardar.', 'success');
                }
                break;
            }
        }
    }

    function refreshImages() {
        window.location.reload();
    }

    document.getElementById('imageModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeImageSelector();
        }
    });
</script>
</body>
</html>