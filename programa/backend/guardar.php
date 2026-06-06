<?php
header('Content-Type: application/json; charset=utf-8');
session_start();
require_once 'conexion.php';

if (!isset($_SESSION['id'])) {
    echo json_encode(["status" => "error", "message" => "No autorizado"]);
    exit;
}

$id_usuario = $_SESSION['id'];
$datos      = json_decode(file_get_contents('php://input'), true);

if (!$datos) {
    echo json_encode(["status" => "error", "message" => "No se recibieron datos válidos"]);
    exit;
}

$idnota    = isset($datos['idnota']) ? $datos['idnota'] : null;
$titulo    = isset($datos['titulo'])    ? trim($datos['titulo'])    : '';
$contenido = isset($datos['contenido']) ? trim($datos['contenido']) : '';

if (empty($titulo)) {
    echo json_encode(["status" => "error", "message" => "El título es obligatorio"]);
    exit;
}

if ($idnota === null || $idnota === '') {
    // Nueva nota
    $stmt = $conn->prepare("INSERT INTO notas (id_usuario, titulo, contenido) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $id_usuario, $titulo, $contenido);
} else {
    // Editar — solo si la nota pertenece al usuario
    $stmt = $conn->prepare("UPDATE notas SET titulo = ?, contenido = ? WHERE idnota = ? AND id_usuario = ?");
    $stmt->bind_param("ssii", $titulo, $contenido, $idnota, $id_usuario);
}

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Nota guardada correctamente"]);
} else {
    echo json_encode(["status" => "error", "message" => "Error: " . $conn->error]);
}

$stmt->close();
$conn->close();
?>