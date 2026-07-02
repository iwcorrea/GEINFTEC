<?php
require_once __DIR__ . '/config.php';

/**
 * Obtiene el valor de una clave de contenido
 */
function getContent($seccion, $clave, $default = '') {
    global $pdo;
    $stmt = $pdo->prepare("SELECT valor FROM contenido WHERE seccion = ? AND clave = ?");
    $stmt->execute([$seccion, $clave]);
    $row = $stmt->fetch();
    return $row ? $row['valor'] : $default;
}

/**
 * Actualiza o inserta un valor de contenido
 */
function updateContent($seccion, $clave, $valor) {
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO contenido (seccion, clave, valor) VALUES (?, ?, ?) 
                            ON CONFLICT (seccion, clave) DO UPDATE SET valor = EXCLUDED.valor");
    // Para MySQL usar: ON DUPLICATE KEY UPDATE valor = VALUES(valor)
    // Pero con PostgreSQL usamos ON CONFLICT
    return $stmt->execute([$seccion, $clave, $valor]);
}

/**
 * Obtiene todos los datos de una sección (clave => valor)
 */
function getSection($seccion) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT clave, valor FROM contenido WHERE seccion = ?");
    $stmt->execute([$seccion]);
    $data = [];
    while ($row = $stmt->fetch()) {
        $data[$row['clave']] = $row['valor'];
    }
    return $data;
}

/**
 * Obtiene todas las secciones y claves (para el admin)
 */
function getAllContent() {
    global $pdo;
    $stmt = $pdo->query("SELECT seccion, clave, valor FROM contenido ORDER BY seccion, clave");
    $data = [];
    while ($row = $stmt->fetch()) {
        $data[$row['seccion']][$row['clave']] = $row['valor'];
    }
    return $data;
}
?>