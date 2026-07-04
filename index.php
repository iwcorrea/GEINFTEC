<?php
// ============================================================
// CONFIGURACIÓN DE ERRORES Y CSP (desde PHP)
// ============================================================
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Establecer CSP desde PHP (prioridad sobre el servidor)
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval' https: data:; style-src 'self' 'unsafe-inline' https:; img-src 'self' data: https:; font-src 'self' data: https:; connect-src 'self' https:;");

// ============================================================
// INCLUIR FUNCIONES Y OBTENER DATOS
// ============================================================
require_once 'funciones.php';

// Variable para depuración
$db_error = false;

try {
    // --- Hero ---
    $hero_titulo = getContent('hero', 'titulo', 'Innovación que');
    $hero_subtitulo = getContent('hero', 'subtitulo', 'Ingeniería, construcción y desarrollo de software con visión de vanguardia. Transformamos ideas en realidades digitales y físicas.');
    $hero_frases = getContent('hero', 'frases', '["Ingeniería de vanguardia","Construcción inteligente","Software que transforma"]');
    $hero_frases_array = json_decode($hero_frases, true) ?: ["Ingeniería de vanguardia","Construcción inteligente","Software que transforma"];

    // --- Servicios ---
    $servicios_titulo = getContent('servicios', 'titulo', 'Nuestros');
    $servicios_sub = getContent('servicios', 'subtitulo', 'Soluciones integrales que combinan ingeniería de calidad con tecnología de punta.');
    $servicios_items = [];
    for ($i = 1; $i <= 6; $i++) {
        $servicios_items[] = [
            'icon' => getContent('servicios', "item{$i}_icon", ''),
            'titulo' => getContent('servicios', "item{$i}_titulo", "Servicio {$i}"),
            'desc' => getContent('servicios', "item{$i}_desc", 'Descripción del servicio.')
        ];
    }

    // --- Proyectos ---
    $proyectos_titulo = getContent('proyectos', 'titulo', 'Proyectos');
    $proyectos_sub = getContent('proyectos', 'subtitulo', 'Obras y soluciones que reflejan nuestra excelencia y compromiso.');
    $proyectos_items = [];
    for ($i = 1; $i <= 3; $i++) {
        $proyectos_items[] = [
            'img' => getContent('proyectos', "item{$i}_img", 'https://images.unsplash.com/photo-1541888946425-d81bb19240f5?w=600&h=400&fit=crop'),
            'titulo' => getContent('proyectos', "item{$i}_titulo", "Proyecto {$i}"),
            'desc' => getContent('proyectos', "item{$i}_desc", 'Descripción del proyecto.')
        ];
    }

    // --- Tecnologías ---
    $tecnologias_titulo = getContent('tecnologias', 'titulo', 'Tecnologías');
    $tecnologias_sub = getContent('tecnologias', 'subtitulo', 'Herramientas y plataformas con las que trabajamos día a día.');
    $tecnologias_raw = getContent('tecnologias', 'lista', '[{"icon":"⚛️","name":"React"},{"icon":"🟢","name":"Node.js"},{"icon":"🐍","name":"Python"},{"icon":"☁️","name":"AWS"},{"icon":"🐳","name":"Docker"},{"icon":"🗄️","name":"PostgreSQL"},{"icon":"📱","name":"Flutter"},{"icon":"🔷","name":"TypeScript"}]');
    $tecnologias_array = json_decode($tecnologias_raw, true);
    if (!is_array($tecnologias_array)) {
        $tecnologias_array = [
            ["icon" => "⚛️", "name" => "React"],
            ["icon" => "🟢", "name" => "Node.js"],
            ["icon" => "🐍", "name" => "Python"],
            ["icon" => "☁️", "name" => "AWS"],
            ["icon" => "🐳", "name" => "Docker"],
            ["icon" => "🗄️", "name" => "PostgreSQL"],
            ["icon" => "📱", "name" => "Flutter"],
            ["icon" => "🔷", "name" => "TypeScript"]
        ];
    }

    // --- Estadísticas ---
    $estadisticas_titulo = getContent('estadisticas', 'titulo', 'En');
    $estadisticas_sub = getContent('estadisticas', 'subtitulo', 'La confianza de nuestros clientes y el impacto de nuestros proyectos.');
    $stats = [
        ['clave' => 'anos', 'label' => 'Años de experiencia', 'default' => 12],
        ['clave' => 'proyectos', 'label' => 'Proyectos entregados', 'default' => 150],
        ['clave' => 'clientes', 'label' => 'Clientes satisfechos', 'default' => 98],
        ['clave' => 'satisfaccion', 'label' => '% Calidad garantizada', 'default' => 100]
    ];
    foreach ($stats as &$stat) {
        $stat['valor'] = getContent('estadisticas', $stat['clave'], $stat['default']);
    }
    unset($stat);

    // --- Equipo ---
    $equipo_titulo = getContent('equipo', 'titulo', 'Nuestro');
    $equipo_sub = getContent('equipo', 'subtitulo', 'Profesionales apasionados por la innovación y la excelencia.');
    $equipo_items = [];
    for ($i = 1; $i <= 4; $i++) {
        $equipo_items[] = [
            'img' => getContent('equipo', "item{$i}_img", 'https://images.unsplash.com/photo-1560250097-0b93528c311a?w=200&h=200&fit=crop&crop=face'),
            'nombre' => getContent('equipo', "item{$i}_nombre", "Miembro {$i}"),
            'cargo' => getContent('equipo', "item{$i}_cargo", 'Cargo'),
            'bio' => getContent('equipo', "item{$i}_bio", 'Biografía del miembro.')
        ];
    }

    // --- Contacto ---
    $contacto_titulo = getContent('contacto', 'titulo', 'Contáctanos');
    $contacto_sub = getContent('contacto', 'subtitulo', 'Estamos listos para hacer realidad tu próximo proyecto.');
    $contacto_direccion = getContent('contacto', 'direccion', 'Bogotá, Colombia');
    $contacto_telefono = getContent('contacto', 'telefono', '+57 300 123 4567');
    $contacto_email = getContent('contacto', 'email', 'contacto@geinftec.com');
    $contacto_horario = getContent('contacto', 'horario', 'Lun – Vie: 8:00 am – 6:00 pm');
    $contacto_mapa = getContent('contacto', 'mapa_embed', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3976.785140536432!2d-74.08373268519861!3d4.624548343699416!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8e3f9a3f5c1b2e6b%3A0x5f7b6c8a0a2b9c0d!2sBogot%C3%A1!5e0!3m2!1ses!2sco!4v1650000000000!5m2!1ses!2sco');

    // --- Redes Sociales ---
    $social_linkedin = getContent('sociales', 'linkedin', '#');
    $social_twitter = getContent('sociales', 'twitter', '#');
    $social_instagram = getContent('sociales', 'instagram', '#');
    $social_youtube = getContent('sociales', 'youtube', '#');

    // --- Footer ---
    $footer_texto = getContent('footer', 'texto', 'Ingeniería, construcción y tecnología para un futuro sostenible e inteligente.');
    $footer_copyright = getContent('footer', 'copyright', '&copy; 2026 GEINFTEC S.A.S. Todos los derechos reservados. | Diseñado con 💙 en Colombia.');

} catch (Exception $e) {
    $db_error = true;
    error_log("Error en index.php: " . $e->getMessage());
    // Valores por defecto (se definen más abajo)
    $hero_titulo = 'Innovación que';
    $hero_subtitulo = 'Cargando contenido...';
    $hero_frases_array = ["Ingeniería de vanguardia", "Construcción inteligente", "Software que transforma"];
    $servicios_titulo = 'Nuestros';
    $servicios_sub = 'Cargando servicios...';
    $servicios_items = [];
    for ($i = 1; $i <= 6; $i++) {
        $servicios_items[] = ['icon' => '🔧', 'titulo' => 'Servicio ' . $i, 'desc' => 'Descripción del servicio.'];
    }
    $proyectos_titulo = 'Proyectos';
    $proyectos_sub = 'Cargando proyectos...';
    $proyectos_items = [];
    for ($i = 1; $i <= 3; $i++) {
        $proyectos_items[] = ['img' => 'https://images.unsplash.com/photo-1541888946425-d81bb19240f5?w=600&h=400&fit=crop', 'titulo' => 'Proyecto ' . $i, 'desc' => 'Descripción del proyecto.'];
    }
    $tecnologias_titulo = 'Tecnologías';
    $tecnologias_sub = 'Cargando tecnologías...';
    $tecnologias_array = [["icon" => "⚛️", "name" => "React"], ["icon" => "🟢", "name" => "Node.js"]];
    $estadisticas_titulo = 'En';
    $estadisticas_sub = 'Cargando estadísticas...';
    $stats = [
        ['clave' => 'anos', 'label' => 'Años de experiencia', 'valor' => 12],
        ['clave' => 'proyectos', 'label' => 'Proyectos entregados', 'valor' => 150],
        ['clave' => 'clientes', 'label' => 'Clientes satisfechos', 'valor' => 98],
        ['clave' => 'satisfaccion', 'label' => '% Calidad garantizada', 'valor' => 100]
    ];
    $equipo_titulo = 'Nuestro';
    $equipo_sub = 'Cargando equipo...';
    $equipo_items = [];
    for ($i = 1; $i <= 4; $i++) {
        $equipo_items[] = ['img' => 'https://images.unsplash.com/photo-1560250097-0b93528c311a?w=200&h=200&fit=crop&crop=face', 'nombre' => 'Miembro ' . $i, 'cargo' => 'Cargo', 'bio' => 'Biografía.'];
    }
    $contacto_titulo = 'Contáctanos';
    $contacto_sub = 'Estamos listos para ayudarte.';
    $contacto_direccion = 'Bogotá, Colombia';
    $contacto_telefono = '+57 300 123 4567';
    $contacto_email = 'contacto@geinftec.com';
    $contacto_horario = 'Lun – Vie: 8:00 am – 6:00 pm';
    $contacto_mapa = 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3976.785140536432!2d-74.08373268519861!3d4.624548343699416!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8e3f9a3f5c1b2e6b%3A0x5f7b6c8a0a2b9c0d!2sBogot%C3%A1!5e0!3m2!1ses!2sco!4v1650000000000!5m2!1ses!2sco';
    $social_linkedin = '#';
    $social_twitter = '#';
    $social_instagram = '#';
    $social_youtube = '#';
    $footer_texto = 'GEINFTEC S.A.S. - Ingeniería y tecnología.';
    $footer_copyright = '&copy; 2026 GEINFTEC S.A.S. Todos los derechos reservados.';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>GEINFTEC S.A.S. – Ingeniería, Construcción y Desarrollo de Software</title>
    <!-- SIN META CSP (se maneja desde PHP) -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="style.css" />
</head>
<body>
    <!-- Mensaje de depuración si hay error en la BD -->
    <?php if ($db_error): ?>
        <div style="background: #ff6b6b; color: #fff; padding: 1rem; text-align: center; font-family: sans-serif; position: fixed; top: 0; left: 0; width: 100%; z-index: 9999;">
            ⚠️ Error al cargar datos desde la base de datos. Mostrando contenido de respaldo. Revisa los logs de Render para más detalles.
        </div>
    <?php endif; ?>

    <!-- Progress Bar -->
    <div id="progress-bar"></div>

    <!-- HEADER -->
    <header class="header" id="header">
        <div class="container">
            <div class="logo">GEINFTEC <span>S.A.S.</span></div>
            <button class="hamburger" id="hamburger" aria-label="Menú">
                <span></span><span></span><span></span>
            </button>
            <ul class="nav-links" id="nav-links">
                <li><a href="#servicios">Servicios</a></li>
                <li><a href="#proyectos">Proyectos</a></li>
                <li><a href="#tecnologias">Tecnologías</a></li>
                <li><a href="#estadisticas">Estadísticas</a></li>
                <li><a href="#equipo">Equipo</a></li>
                <li><a href="#contacto">Contacto</a></li>
            </ul>
        </div>
    </header>

    <!-- HERO -->
    <section class="hero" id="hero">
        <canvas id="hero-canvas"></canvas>
        <div class="container hero-content">
            <h1 class="hero-title">
                <?php echo htmlspecialchars($hero_titulo); ?><br />
                <span class="highlight">construye el futuro</span>
            </h1>
            <div style="font-size: 2rem; font-weight: 600; margin-bottom: 0.5rem;">
                <span id="rotating-text" data-phrases='<?php echo json_encode($hero_frases_array); ?>'></span>
            </div>
            <p class="hero-sub"><?php echo htmlspecialchars($hero_subtitulo); ?></p>
            <a href="#contacto" class="btn">Contáctanos</a>
            <a href="#proyectos" class="btn btn-outline" style="margin-left: 1rem;">Ver proyectos</a>
        </div>
    </section>

    <!-- SERVICIOS -->
    <section id="servicios">
        <div class="container">
            <h2 class="section-title fade-up"><?php echo htmlspecialchars($servicios_titulo); ?> <span>Servicios</span></h2>
            <p class="section-sub fade-up"><?php echo htmlspecialchars($servicios_sub); ?></p>
            <div class="grid-3">
                <?php foreach ($servicios_items as $item): ?>
                <div class="card service-card fade-up">
                    <span class="icon"><?php echo htmlspecialchars($item['icon']); ?></span>
                    <h3><?php echo htmlspecialchars($item['titulo']); ?></h3>
                    <p><?php echo htmlspecialchars($item['desc']); ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- PROYECTOS -->
    <section id="proyectos" style="background: rgba(0,0,0,0.2);">
        <div class="container">
            <h2 class="section-title fade-up"><?php echo htmlspecialchars($proyectos_titulo); ?> <span>Destacados</span></h2>
            <p class="section-sub fade-up"><?php echo htmlspecialchars($proyectos_sub); ?></p>
            <div class="grid-3">
                <?php foreach ($proyectos_items as $item): ?>
                <div class="card project-card fade-up">
                    <img src="<?php echo htmlspecialchars($item['img']); ?>" alt="<?php echo htmlspecialchars($item['titulo']); ?>" loading="lazy" />
                    <div class="content">
                        <h3><?php echo htmlspecialchars($item['titulo']); ?></h3>
                        <p><?php echo htmlspecialchars($item['desc']); ?></p>
                        <a href="#" class="btn btn-outline" style="padding:0.4rem 1.2rem; font-size:0.9rem;">Ver más</a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- TECNOLOGÍAS -->
    <section id="tecnologias">
        <div class="container">
            <h2 class="section-title fade-up"><?php echo htmlspecialchars($tecnologias_titulo); ?> <span>que impulsamos</span></h2>
            <p class="section-sub fade-up"><?php echo htmlspecialchars($tecnologias_sub); ?></p>
            <div class="tech-grid fade-up">
                <?php foreach ($tecnologias_array as $tech): ?>
                <div class="tech-item">
                    <span class="icon"><?php echo htmlspecialchars($tech['icon']); ?></span>
                    <?php echo htmlspecialchars($tech['name']); ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- ESTADÍSTICAS -->
    <section id="estadisticas" style="background: rgba(0,0,0,0.15);">
        <div class="container">
            <h2 class="section-title fade-up"><?php echo htmlspecialchars($estadisticas_titulo); ?> <span>números</span></h2>
            <p class="section-sub fade-up"><?php echo htmlspecialchars($estadisticas_sub); ?></p>
            <div class="grid-4">
                <?php foreach ($stats as $stat): ?>
                <div class="stat-card fade-up">
                    <div class="stat-number" data-count="<?php echo htmlspecialchars($stat['valor']); ?>">0</div>
                    <div class="stat-label"><?php echo htmlspecialchars($stat['label']); ?></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- EQUIPO -->
    <section id="equipo">
        <div class="container">
            <h2 class="section-title fade-up"><?php echo htmlspecialchars($equipo_titulo); ?> <span>Equipo</span></h2>
            <p class="section-sub fade-up"><?php echo htmlspecialchars($equipo_sub); ?></p>
            <div class="grid-4">
                <?php foreach ($equipo_items as $item): ?>
                <div class="team-card fade-up">
                    <img src="<?php echo htmlspecialchars($item['img']); ?>" alt="<?php echo htmlspecialchars($item['nombre']); ?>" loading="lazy" />
                    <h4><?php echo htmlspecialchars($item['nombre']); ?></h4>
                    <div class="role"><?php echo htmlspecialchars($item['cargo']); ?></div>
                    <div class="bio"><?php echo htmlspecialchars($item['bio']); ?></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- CONTACTO -->
    <section id="contacto" style="background: rgba(0,0,0,0.2);">
        <div class="container">
            <h2 class="section-title fade-up"><?php echo htmlspecialchars($contacto_titulo); ?> <span>ahora</span></h2>
            <p class="section-sub fade-up"><?php echo htmlspecialchars($contacto_sub); ?></p>
            <div class="contact-grid">
                <div class="fade-up">
                    <form class="contact-form" id="contactForm" novalidate>
                        <input type="text" placeholder="Nombre completo" required id="nombre" />
                        <input type="email" placeholder="Correo electrónico" required id="email" />
                        <input type="text" placeholder="Teléfono" id="telefono" />
                        <textarea rows="4" placeholder="Mensaje" required id="mensaje"></textarea>
                        <button type="submit" class="btn">Enviar mensaje</button>
                        <div id="formFeedback" style="margin-top:1rem; color:var(--cyan);"></div>
                    </form>
                </div>
                <div class="contact-info fade-up">
                    <div class="item"><span class="icon">📍</span> <?php echo htmlspecialchars($contacto_direccion); ?></div>
                    <div class="item"><span class="icon">📞</span> <?php echo htmlspecialchars($contacto_telefono); ?></div>
                    <div class="item"><span class="icon">✉️</span> <?php echo htmlspecialchars($contacto_email); ?></div>
                    <div class="item"><span class="icon">🕒</span> <?php echo htmlspecialchars($contacto_horario); ?></div>
                    <div class="map-container">
                        <iframe src="<?php echo htmlspecialchars($contacto_mapa); ?>" allowfullscreen loading="lazy"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div>
                    <div class="logo" style="font-size:1.8rem; margin-bottom:0.5rem;">GEINFTEC <span>S.A.S.</span></div>
                    <p style="color:var(--text-muted); max-width:300px;"><?php echo htmlspecialchars($footer_texto); ?></p>
                    <div class="socials" style="margin-top:1rem;">
                        <a href="<?php echo htmlspecialchars($social_linkedin); ?>" aria-label="LinkedIn">🔗</a>
                        <a href="<?php echo htmlspecialchars($social_twitter); ?>" aria-label="Twitter">🐦</a>
                        <a href="<?php echo htmlspecialchars($social_instagram); ?>" aria-label="Instagram">📸</a>
                        <a href="<?php echo htmlspecialchars($social_youtube); ?>" aria-label="YouTube">▶️</a>
                    </div>
                </div>
                <div>
                    <h4>Enlaces</h4>
                    <ul style="list-style:none; display:flex; flex-direction:column; gap:0.5rem;">
                        <li><a href="#servicios">Servicios</a></li>
                        <li><a href="#proyectos">Proyectos</a></li>
                        <li><a href="#equipo">Equipo</a></li>
                        <li><a href="#contacto">Contacto</a></li>
                    </ul>
                </div>
                <div>
                    <h4>Newsletter</h4>
                    <p style="color:var(--text-muted); font-size:0.9rem;">Recibe novedades y ofertas especiales.</p>
                    <form class="newsletter" id="newsletterForm">
                        <input type="email" placeholder="Tu correo" required />
                        <button type="submit">Suscribir</button>
                    </form>
                    <div id="newsletterFeedback" style="margin-top:0.5rem; color:var(--cyan); font-size:0.9rem;"></div>
                </div>
            </div>
            <div class="copyright">
                <?php echo htmlspecialchars($footer_copyright); ?>
                <br>
                <span style="font-size:0.8rem; opacity:0.5;">
                    <a href="login.php" style="color: #b0b8d1; text-decoration:none;">🔐 Admin</a>
                </span>
            </div>
        </div>
    </footer>

    <!-- Botón volver arriba -->
    <button id="back-to-top" aria-label="Volver arriba">↑</button>

    <script src="script.js"></script>
</body>
</html>