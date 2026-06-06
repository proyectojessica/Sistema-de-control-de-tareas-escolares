<?php
header('Content-Type: application/json; charset=utf-8');
session_start();
require_once 'conexion.php';

if (!isset($_SESSION['id'])) {
    echo json_encode([]);
    exit;
}

$id_usuario = $_SESSION['id'];
$stmt = $conn->prepare("SELECT idnota, titulo, contenido, fecha FROM notas WHERE id_usuario = ? ORDER BY idnota DESC");
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$resultado = $stmt->get_result();

$notas = [];
while ($fila = $resultado->fetch_assoc()) {
    $notas[] = $fila;
}

echo json_encode($notas);
$conn->close();
?>