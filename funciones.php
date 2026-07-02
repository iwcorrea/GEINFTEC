<?php
require_once 'config.php';

function getContent($seccion, $clave, $default = '') {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT valor FROM contenido WHERE seccion = :seccion AND clave = :clave");
        $stmt->execute(['seccion' => $seccion, 'clave' => $clave]);
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
    global $pdo;
    try {
        $stmt = $pdo->prepare("INSERT INTO contenido (seccion, clave, valor) VALUES (:seccion, :clave, :valor)
                                ON CONFLICT (seccion, clave) DO UPDATE SET valor = EXCLUDED.valor");
        $stmt->execute(['seccion' => $seccion, 'clave' => $clave, 'valor' => $valor]);
        return true;
    } catch (PDOException $e) {
        error_log("Error en updateContent: " . $e->getMessage());
        return false;
    }
}

function getSection($seccion) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT clave, valor FROM contenido WHERE seccion = :seccion");
        $stmt->execute(['seccion' => $seccion]);
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
    global $pdo;
    try {
        $stmt = $pdo->query("SELECT seccion, clave, valor FROM contenido ORDER BY seccion, clave");
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