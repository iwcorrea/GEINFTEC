-- Estructura de tabla para `contenido` (PostgreSQL)
CREATE TABLE IF NOT EXISTS contenido (
    id SERIAL PRIMARY KEY,
    seccion VARCHAR(50) NOT NULL,
    clave VARCHAR(50) NOT NULL,
    valor TEXT NOT NULL,
    UNIQUE(seccion, clave)
);

-- Insertar datos por defecto (con claves para imágenes de proyectos y equipo)
INSERT INTO contenido (seccion, clave, valor) VALUES
('hero', 'titulo', 'Innovación que'),
('hero', 'subtitulo', 'Ingeniería, construcción y desarrollo de software con visión de vanguardia. Transformamos ideas en realidades digitales y físicas.'),
('hero', 'frases', '["Ingeniería de vanguardia","Construcción inteligente","Software que transforma"]'),
('servicios', 'titulo', 'Nuestros'),
('servicios', 'subtitulo', 'Soluciones integrales que combinan ingeniería de calidad con tecnología de punta.'),
('proyectos', 'titulo', 'Proyectos'),
('proyectos', 'subtitulo', 'Obras y soluciones que reflejan nuestra excelencia y compromiso.'),
('proyectos', 'proyecto1_imagen', 'https://images.unsplash.com/photo-1541888946425-d81bb19240f5?w=600&h=400&fit=crop'),
('proyectos', 'proyecto2_imagen', 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=600&h=400&fit=crop'),
('proyectos', 'proyecto3_imagen', 'https://images.unsplash.com/photo-1581091226825-a6a2a5aee158?w=600&h=400&fit=crop'),
('tecnologias', 'titulo', 'Tecnologías'),
('tecnologias', 'subtitulo', 'Herramientas y plataformas con las que trabajamos día a día.'),
('estadisticas', 'titulo', 'En'),
('estadisticas', 'subtitulo', 'La confianza de nuestros clientes y el impacto de nuestros proyectos.'),
('estadisticas', 'anos', '12'),
('estadisticas', 'proyectos', '150'),
('estadisticas', 'clientes', '98'),
('estadisticas', 'satisfaccion', '100'),
('equipo', 'titulo', 'Nuestro'),
('equipo', 'subtitulo', 'Profesionales apasionados por la innovación y la excelencia.'),
('equipo', 'miembro1_imagen', 'https://images.unsplash.com/photo-1560250097-0b93528c311a?w=200&h=200&fit=crop&crop=face'),
('equipo', 'miembro2_imagen', 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=200&h=200&fit=crop&crop=face'),
('equipo', 'miembro3_imagen', 'https://images.unsplash.com/photo-1580489944761-15a19d654956?w=200&h=200&fit=crop&crop=face'),
('equipo', 'miembro4_imagen', 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=200&h=200&fit=crop&crop=face'),
('contacto', 'titulo', 'Contáctanos'),
('contacto', 'subtitulo', 'Estamos listos para hacer realidad tu próximo proyecto.'),
('footer', 'texto', 'Ingeniería, construcción y tecnología para un futuro sostenible e inteligente.'),
('footer', 'copyright', '&copy; 2026 GEINFTEC S.A.S. Todos los derechos reservados. | Diseñado con 💙 en Colombia.')
ON CONFLICT (seccion, clave) DO NOTHING;