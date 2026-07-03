<?php
require_once 'config.php';

/**
 * Obtiene el valor de una clave de contenido
 */
function getContent($seccion, $clave, $default = '') {
    global $db_conn;
    $result = pg_query_params($db_conn, 'SELECT valor FROM contenido WHERE seccion = $1 AND clave = $2', [$seccion, $clave]);
    if ($result && pg_num_rows($result) > 0) {
        $row = pg_fetch_assoc($result);
        return $row['valor'];
    }
    return $default;
}

/**
 * Actualiza o inserta un valor de contenido
 */
function updateContent($seccion, $clave, $valor) {
    global $db_conn;
    $result = pg_query_params($db_conn, 
        'INSERT INTO contenido (seccion, clave, valor) VALUES ($1, $2, $3) ON CONFLICT (seccion, clave) DO UPDATE SET valor = EXCLUDED.valor',
        [$seccion, $clave, $valor]
    );
    return $result !== false;
}

/**
 * Obtiene todos los datos de una sección
 */
function getSection($seccion) {
    global $db_conn;
    $result = pg_query_params($db_conn, 'SELECT clave, valor FROM contenido WHERE seccion = $1', [$seccion]);
    $data = [];
    if ($result) {
        while ($row = pg_fetch_assoc($result)) {
            $data[$row['clave']] = $row['valor'];
        }
    }
    return $data;
}

/**
 * Obtiene todo el contenido
 */
function getAllContent() {
    global $db_conn;
    $result = pg_query($db_conn, 'SELECT seccion, clave, valor FROM contenido ORDER BY seccion, clave');
    $data = [];
    if ($result) {
        while ($row = pg_fetch_assoc($result)) {
            $data[$row['seccion']][$row['clave']] = $row['valor'];
        }
    }
    return $data;
}

/**
 * Función para agregar una nueva clave (útil para extender el panel)
 */
function addContent($seccion, $clave, $valor = '') {
    global $db_conn;
    $result = pg_query_params($db_conn, 
        'INSERT INTO contenido (seccion, clave, valor) VALUES ($1, $2, $3) ON CONFLICT (seccion, clave) DO NOTHING',
        [$seccion, $clave, $valor]
    );
    return $result !== false;
}

/**
 * Elimina una clave (cuidado, solo para administradores avanzados)
 */
function deleteContent($seccion, $clave) {
    global $db_conn;
    $result = pg_query_params($db_conn, 'DELETE FROM contenido WHERE seccion = $1 AND clave = $2', [$seccion, $clave]);
    return $result !== false;
}

/**
 * Obtiene las secciones disponibles
 */
function getSections() {
    global $db_conn;
    $result = pg_query($db_conn, 'SELECT DISTINCT seccion FROM contenido ORDER BY seccion');
    $sections = [];
    if ($result) {
        while ($row = pg_fetch_assoc($result)) {
            $sections[] = $row['seccion'];
        }
    }
    return $sections;
}
?>