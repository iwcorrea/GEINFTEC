<?php
require_once 'config.php';

function getContent($seccion, $clave, $default = '') {
    global $db_conn;
    $result = pg_query_params($db_conn, 'SELECT valor FROM contenido WHERE seccion = $1 AND clave = $2', [$seccion, $clave]);
    if ($result && pg_num_rows($result) > 0) {
        $row = pg_fetch_assoc($result);
        return $row['valor'];
    }
    return $default;
}

function updateContent($seccion, $clave, $valor) {
    global $db_conn;
    $result = pg_query_params($db_conn, 'INSERT INTO contenido (seccion, clave, valor) VALUES ($1, $2, $3) ON CONFLICT (seccion, clave) DO UPDATE SET valor = EXCLUDED.valor', [$seccion, $clave, $valor]);
    return $result !== false;
}

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
?>