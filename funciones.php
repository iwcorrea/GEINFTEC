<?php
require_once 'config.php';

function getContent($seccion, $clave, $default = '') {
    global $conn;
    try {
        $stmt = $conn->prepare("SELECT valor FROM contenido WHERE seccion = ? AND clave = ?");
        $stmt->execute([$seccion, $clave]);
        $row = $stmt->fetch();
        if ($row) {
            return $row['valor'];
        }
    } catch (PDOException $e) {
        error_log("Error en getContent: " . $e->getMessage());
    }
    return $default;
}

function updateContent($seccion, $clave, $valor) {
    global $conn;
    try {
        $stmt = $conn->prepare("INSERT INTO contenido (seccion, clave, valor) VALUES (?, ?, ?) 
                                ON CONFLICT (seccion, clave) DO UPDATE SET valor = EXCLUDED.valor");
        return $stmt->execute([$seccion, $clave, $valor]);
    } catch (PDOException $e) {
        error_log("Error en updateContent: " . $e->getMessage());
        return false;
    }
}

function getSection($seccion) {
    global $conn;
    try {
        $stmt = $conn->prepare("SELECT clave, valor FROM contenido WHERE seccion = ?");
        $stmt->execute([$seccion]);
        $data = [];
        while ($row = $stmt->fetch()) {
            $data[$row['clave']] = $row['valor'];
        }
        return $data;
    } catch (PDOException $e) {
        error_log("Error en getSection: " . $e->getMessage());
        return [];
    }
}

function getAllContent() {
    global $conn;
    try {
        $stmt = $conn->query("SELECT seccion, clave, valor FROM contenido ORDER BY seccion, clave");
        $data = [];
        while ($row = $stmt->fetch()) {
            $data[$row['seccion']][$row['clave']] = $row['valor'];
        }
        return $data;
    } catch (PDOException $e) {
        error_log("Error en getAllContent: " . $e->getMessage());
        return [];
    }
}
?>