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
$id         = isset($datos['idnota']) ? $datos['idnota'] : null;

if ($id !== null && $id !== '') {
    // Solo elimina si la nota pertenece al usuario
    $stmt = $conn->prepare("DELETE FROM notas WHERE idnota = ? AND id_usuario = ?");
    $stmt->bind_param("ii", $id, $id_usuario);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Nota eliminada correctamente"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error: " . $conn->error]);
    }
    $stmt->close();
} else {
    echo json_encode(["status" => "error", "message" => "No se proporcionó un ID válido"]);
}

$conn->close();
?>