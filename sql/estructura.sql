-- Estructura de tabla para `contenido` (PostgreSQL)
CREATE TABLE IF NOT EXISTS contenido (
    id SERIAL PRIMARY KEY,
    seccion VARCHAR(50) NOT NULL,
    clave VARCHAR(50) NOT NULL,
    valor TEXT NOT NULL,
    UNIQUE(seccion, clave)
);

-- Insertar datos por defecto
INSERT INTO contenido (seccion, clave, valor) VALUES
('hero', 'titulo', 'Innovación que construye el futuro'),
('hero', 'subtitulo', 'Ingeniería, construcción y desarrollo de software con visión de vanguardia.'),
('hero', 'frases', '["Ingeniería de vanguardia","Construcción inteligente","Software que transforma"]'),
('hero', 'imagen_fondo', ''),
('servicios', 'titulo', 'Nuestros Servicios'),
('servicios', 'subtitulo', 'Soluciones integrales que combinan ingeniería de calidad con tecnología de punta.'),
('proyectos', 'titulo', 'Proyectos Destacados'),
('proyectos', 'subtitulo', 'Obras y soluciones que reflejan nuestra excelencia y compromiso.'),
('tecnologias', 'titulo', 'Tecnologías que impulsamos'),
('tecnologias', 'subtitulo', 'Herramientas y plataformas con las que trabajamos día a día.'),
('estadisticas', 'titulo', 'En números'),
('estadisticas', 'subtitulo', 'La confianza de nuestros clientes y el impacto de nuestros proyectos.'),
('equipo', 'titulo', 'Nuestro Equipo'),
('equipo', 'subtitulo', 'Profesionales apasionados por la innovación y la excelencia.'),
('contacto', 'titulo', 'Contáctanos ahora'),
('contacto', 'subtitulo', 'Estamos listos para hacer realidad tu próximo proyecto.'),
('footer', 'texto', 'Ingeniería, construcción y tecnología para un futuro sostenible e inteligente.'),
('footer', 'copyright', '&copy; 2026 GEINFTEC S.A.S. Todos los derechos reservados. | Diseñado con 💙 en Colombia.');