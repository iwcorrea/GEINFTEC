<?php
// Cabeceras de seguridad (ajustadas)
header("X-Frame-Options: SAMEORIGIN");
header("X-Content-Type-Options: nosniff");
header("Referrer-Policy: strict-origin-when-cross-origin");
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' https:; style-src 'self' 'unsafe-inline'; img-src 'self' data: https:; font-src 'self' data: https:; connect-src 'self' https:; frame-src https://www.google.com;");
require_once 'funciones.php';

// Obtener datos de la BD con fallbacks
$hero_titulo = getContent('hero', 'titulo', 'Innovación que');
$hero_subtitulo = getContent('hero', 'subtitulo', 'Ingeniería, construcción y desarrollo de software con visión de vanguardia. Transformamos ideas en realidades digitales y físicas.');
$hero_frases = getContent('hero', 'frases', '["Ingeniería de vanguardia","Construcción inteligente","Software que transforma"]');
$hero_frases_array = json_decode($hero_frases, true) ?: ["Ingeniería de vanguardia","Construcción inteligente","Software que transforma"];

$servicios_titulo = getContent('servicios', 'titulo', 'Nuestros');
$servicios_sub = getContent('servicios', 'subtitulo', 'Soluciones integrales que combinan ingeniería de calidad con tecnología de punta.');

$proyectos_titulo = getContent('proyectos', 'titulo', 'Proyectos');
$proyectos_sub = getContent('proyectos', 'subtitulo', 'Obras y soluciones que reflejan nuestra excelencia y compromiso.');

$tecnologias_titulo = getContent('tecnologias', 'titulo', 'Tecnologías');
$tecnologias_sub = getContent('tecnologias', 'subtitulo', 'Herramientas y plataformas con las que trabajamos día a día.');

$estadisticas_titulo = getContent('estadisticas', 'titulo', 'En');
$estadisticas_sub = getContent('estadisticas', 'subtitulo', 'La confianza de nuestros clientes y el impacto de nuestros proyectos.');

$equipo_titulo = getContent('equipo', 'titulo', 'Nuestro');
$equipo_sub = getContent('equipo', 'subtitulo', 'Profesionales apasionados por la innovación y la excelencia.');

$contacto_titulo = getContent('contacto', 'titulo', 'Contáctanos');
$contacto_sub = getContent('contacto', 'subtitulo', 'Estamos listos para hacer realidad tu próximo proyecto.');

$footer_texto = getContent('footer', 'texto', 'Ingeniería, construcción y tecnología para un futuro sostenible e inteligente.');
$footer_copyright = getContent('footer', 'copyright', '&copy; 2026 GEINFTEC S.A.S. Todos los derechos reservados. | Diseñado con 💙 en Colombia.');

// Estadísticas
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
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>GEINFTEC S.A.S. – Ingeniería, Construcción y Desarrollo de Software</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="style.css" />
</head>
<body>

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
                <?php echo $hero_titulo; ?><br />
                <span class="highlight">construye el futuro</span>
            </h1>
            <div style="font-size: 2rem; font-weight: 600; margin-bottom: 0.5rem;">
                <span id="rotating-text" data-phrases='<?php echo json_encode($hero_frases_array); ?>'></span>
            </div>
            <p class="hero-sub"><?php echo $hero_subtitulo; ?></p>
            <a href="#contacto" class="btn">Contáctanos</a>
            <a href="#proyectos" class="btn btn-outline" style="margin-left: 1rem;">Ver proyectos</a>
        </div>
    </section>

    <!-- SERVICIOS -->
    <section id="servicios">
        <div class="container">
            <h2 class="section-title fade-up"><?php echo $servicios_titulo; ?> <span>Servicios</span></h2>
            <p class="section-sub fade-up"><?php echo $servicios_sub; ?></p>
            <div class="grid-3">
                <div class="card service-card fade-up">
                    <span class="icon">🏗️</span>
                    <h3>Ingeniería Civil</h3>
                    <p>Diseño y construcción de infraestructura resistente y sostenible.</p>
                </div>
                <div class="card service-card fade-up">
                    <span class="icon">💻</span>
                    <h3>Desarrollo Software</h3>
                    <p>Aplicaciones web, móviles y sistemas a la medida con arquitecturas modernas.</p>
                </div>
                <div class="card service-card fade-up">
                    <span class="icon">🔧</span>
                    <h3>Consultoría TI</h3>
                    <p>Asesoramiento estratégico para transformación digital y optimización de procesos.</p>
                </div>
                <div class="card service-card fade-up">
                    <span class="icon">📊</span>
                    <h3>Data & Analytics</h3>
                    <p>Inteligencia de negocio, dashboards y análisis predictivo para tomar mejores decisiones.</p>
                </div>
                <div class="card service-card fade-up">
                    <span class="icon">🌐</span>
                    <h3>Infraestructura Cloud</h3>
                    <p>Migración y gestión de entornos en la nube con alta disponibilidad y seguridad.</p>
                </div>
                <div class="card service-card fade-up">
                    <span class="icon">🔒</span>
                    <h3>Ciberseguridad</h3>
                    <p>Auditorías, protección de datos y cumplimiento normativo para tu empresa.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- PROYECTOS -->
    <section id="proyectos" style="background: rgba(0,0,0,0.2);">
        <div class="container">
            <h2 class="section-title fade-up"><?php echo $proyectos_titulo; ?> <span>Destacados</span></h2>
            <p class="section-sub fade-up"><?php echo $proyectos_sub; ?></p>
            <div class="grid-3">
                <div class="card project-card fade-up">
                    <img src="https://images.unsplash.com/photo-1541888946425-d81bb19240f5?w=600&h=400&fit=crop" alt="Construcción de puente" loading="lazy" />
                    <div class="content">
                        <h3>Puente Colgante Moderno</h3>
                        <p>Diseño estructural y construcción de un puente peatonal con materiales compuestos.</p>
                        <a href="#" class="btn btn-outline" style="padding:0.4rem 1.2rem; font-size:0.9rem;">Ver más</a>
                    </div>
                </div>
                <div class="card project-card fade-up">
                    <img src="https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=600&h=400&fit=crop" alt="Dashboard software" loading="lazy" />
                    <div class="content">
                        <h3>Plataforma de Gestión</h3>
                        <p>Sistema ERP para empresas de construcción con módulos de inventario y finanzas.</p>
                        <a href="#" class="btn btn-outline" style="padding:0.4rem 1.2rem; font-size:0.9rem;">Ver más</a>
                    </div>
                </div>
                <div class="card project-card fade-up">
                    <img src="https://images.unsplash.com/photo-1581091226825-a6a2a5aee158?w=600&h=400&fit=crop" alt="Edificio inteligente" loading="lazy" />
                    <div class="content">
                        <h3>Edificio Inteligente</h3>
                        <p>Automatización y control de iluminación, climatización y seguridad mediante IoT.</p>
                        <a href="#" class="btn btn-outline" style="padding:0.4rem 1.2rem; font-size:0.9rem;">Ver más</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- TECNOLOGÍAS -->
    <section id="tecnologias">
        <div class="container">
            <h2 class="section-title fade-up"><?php echo $tecnologias_titulo; ?> <span>que impulsamos</span></h2>
            <p class="section-sub fade-up"><?php echo $tecnologias_sub; ?></p>
            <div class="tech-grid fade-up">
                <div class="tech-item"><span class="icon">⚛️</span> React</div>
                <div class="tech-item"><span class="icon">🟢</span> Node.js</div>
                <div class="tech-item"><span class="icon">🐍</span> Python</div>
                <div class="tech-item"><span class="icon">☁️</span> AWS</div>
                <div class="tech-item"><span class="icon">🐳</span> Docker</div>
                <div class="tech-item"><span class="icon">🗄️</span> PostgreSQL</div>
                <div class="tech-item"><span class="icon">📱</span> Flutter</div>
                <div class="tech-item"><span class="icon">🔷</span> TypeScript</div>
            </div>
        </div>
    </section>

    <!-- ESTADÍSTICAS -->
    <section id="estadisticas" style="background: rgba(0,0,0,0.15);">
        <div class="container">
            <h2 class="section-title fade-up"><?php echo $estadisticas_titulo; ?> <span>números</span></h2>
            <p class="section-sub fade-up"><?php echo $estadisticas_sub; ?></p>
            <div class="grid-4">
                <?php foreach ($stats as $stat): ?>
                <div class="stat-card fade-up">
                    <div class="stat-number" data-count="<?php echo $stat['valor']; ?>">0</div>
                    <div class="stat-label"><?php echo $stat['label']; ?></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- EQUIPO -->
    <section id="equipo">
        <div class="container">
            <h2 class="section-title fade-up"><?php echo $equipo_titulo; ?> <span>Equipo</span></h2>
            <p class="section-sub fade-up"><?php echo $equipo_sub; ?></p>
            <div class="grid-4">
                <div class="team-card fade-up">
                    <img src="https://images.unsplash.com/photo-1560250097-0b93528c311a?w=200&h=200&fit=crop&crop=face" alt="CEO" loading="lazy" />
                    <h4>Carlos Mendoza</h4>
                    <div class="role">CEO & Fundador</div>
                    <div class="bio">Ingeniero civil con 20 años de experiencia en grandes infraestructuras.</div>
                </div>
                <div class="team-card fade-up">
                    <img src="https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=200&h=200&fit=crop&crop=face" alt="CTO" loading="lazy" />
                    <h4>Laura Fernández</h4>
                    <div class="role">CTO</div>
                    <div class="bio">Especialista en arquitectura de software y sistemas distribuidos.</div>
                </div>
                <div class="team-card fade-up">
                    <img src="https://images.unsplash.com/photo-1580489944761-15a19d654956?w=200&h=200&fit=crop&crop=face" alt="PM" loading="lazy" />
                    <h4>María Gómez</h4>
                    <div class="role">Project Manager</div>
                    <div class="bio">Lidera equipos multidisciplinarios con metodologías ágiles.</div>
                </div>
                <div class="team-card fade-up">
                    <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=200&h=200&fit=crop&crop=face" alt="Ingeniero" loading="lazy" />
                    <h4>Andrés Ruiz</h4>
                    <div class="role">Ingeniero de Software</div>
                    <div class="bio">Desarrollador full-stack apasionado por la IA y el cloud computing.</div>
                </div>
            </div>
        </div>
    </section>

    <!-- CONTACTO -->
    <section id="contacto" style="background: rgba(0,0,0,0.2);">
        <div class="container">
            <h2 class="section-title fade-up"><?php echo $contacto_titulo; ?> <span>ahora</span></h2>
            <p class="section-sub fade-up"><?php echo $contacto_sub; ?></p>
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
                    <div class="item"><span class="icon">📍</span> Bogotá, Colombia</div>
                    <div class="item"><span class="icon">📞</span> +57 300 123 4567</div>
                    <div class="item"><span class="icon">✉️</span> contacto@geinftec.com</div>
                    <div class="item"><span class="icon">🕒</span> Lun – Vie: 8:00 am – 6:00 pm</div>
                    <div class="map-container">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3976.785140536432!2d-74.08373268519861!3d4.624548343699416!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8e3f9a3f5c1b2e6b%3A0x5f7b6c8a0a2b9c0d!2sBogot%C3%A1!5e0!3m2!1ses!2sco" allowfullscreen loading="lazy"></iframe>
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
                    <p style="color:var(--text-muted); max-width:300px;"><?php echo $footer_texto; ?></p>
                    <div class="socials" style="margin-top:1rem;">
                        <a href="#" aria-label="LinkedIn">🔗</a>
                        <a href="#" aria-label="Twitter">🐦</a>
                        <a href="#" aria-label="Instagram">📸</a>
                        <a href="#" aria-label="YouTube">▶️</a>
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
                <?php echo $footer_copyright; ?>
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