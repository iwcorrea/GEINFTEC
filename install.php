<?php
require_once 'config.php';

try {
    // Crear tabla
    $sql = "CREATE TABLE IF NOT EXISTS contenido (
        id SERIAL PRIMARY KEY,
        seccion VARCHAR(50) NOT NULL,
        clave VARCHAR(50) NOT NULL,
        valor TEXT NOT NULL,
        UNIQUE(seccion, clave)
    )";
    $conn->exec($sql);
    echo "✅ Tabla 'contenido' creada o ya existente.<br>";

    // Insertar datos por defecto
    $datos = [
        ['hero', 'titulo', 'Innovación que'],
        ['hero', 'subtitulo', 'Ingeniería, construcción y desarrollo de software con visión de vanguardia. Transformamos ideas en realidades digitales y físicas.'],
        ['hero', 'frases', '["Ingeniería de vanguardia","Construcción inteligente","Software que transforma"]'],
        ['servicios', 'titulo', 'Nuestros'],
        ['servicios', 'subtitulo', 'Soluciones integrales que combinan ingeniería de calidad con tecnología de punta.'],
        ['proyectos', 'titulo', 'Proyectos'],
        ['proyectos', 'subtitulo', 'Obras y soluciones que reflejan nuestra excelencia y compromiso.'],
        ['tecnologias', 'titulo', 'Tecnologías'],
        ['tecnologias', 'subtitulo', 'Herramientas y plataformas con las que trabajamos día a día.'],
        ['estadisticas', 'titulo', 'En'],
        ['estadisticas', 'subtitulo', 'La confianza de nuestros clientes y el impacto de nuestros proyectos.'],
        ['estadisticas', 'anos', '12'],
        ['estadisticas', 'proyectos', '150'],
        ['estadisticas', 'clientes', '98'],
        ['estadisticas', 'satisfaccion', '100'],
        ['equipo', 'titulo', 'Nuestro'],
        ['equipo', 'subtitulo', 'Profesionales apasionados por la innovación y la excelencia.'],
        ['contacto', 'titulo', 'Contáctanos'],
        ['contacto', 'subtitulo', 'Estamos listos para hacer realidad tu próximo proyecto.'],
        ['footer', 'texto', 'Ingeniería, construcción y tecnología para un futuro sostenible e inteligente.'],
        ['footer', 'copyright', '&copy; 2026 GEINFTEC S.A.S. Todos los derechos reservados. | Diseñado con 💙 en Colombia.']
    ];

    $stmt = $conn->prepare("INSERT INTO contenido (seccion, clave, valor) VALUES (?, ?, ?) ON CONFLICT (seccion, clave) DO NOTHING");
    foreach ($datos as $row) {
        $stmt->execute($row);
    }
    echo "✅ Datos insertados correctamente.<br>";
    echo "<a href='admin.php'>Ir al panel de administración</a>";

} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>