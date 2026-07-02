<?php
require_once 'config.php';

function getContent($seccion, $clave, $default = '') {
    global $conn;
    $stmt = $conn->prepare("SELECT valor FROM contenido WHERE seccion = ? AND clave = ?");
    $stmt->bind_param("ss", $seccion, $clave);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        return $row['valor'];
    }
    return $default;
}

function updateContent($seccion, $clave, $valor) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO contenido (seccion, clave, valor) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE valor = ?");
    $stmt->bind_param("ssss", $seccion, $clave, $valor, $valor);
    return $stmt->execute();
}

function getSection($seccion) {
    global $conn;
    $stmt = $conn->prepare("SELECT clave, valor FROM contenido WHERE seccion = ?");
    $stmt->bind_param("s", $seccion);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[$row['clave']] = $row['valor'];
    }
    return $data;
}

function getAllContent() {
    global $conn;
    $result = $conn->query("SELECT seccion, clave, valor FROM contenido ORDER BY seccion, clave");
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[$row['seccion']][$row['clave']] = $row['valor'];
    }
    return $data;
}
?>