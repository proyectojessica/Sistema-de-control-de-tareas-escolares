<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');

require_once 'conexion.php';

$data     = json_decode(file_get_contents('php://input'), true);
$nombre   = trim($data['nombre']   ?? '');
$apellido = trim($data['apellido'] ?? '');
$email    = trim($data['email']    ?? '');
$password = trim($data['password'] ?? '');
$grado    = trim($data['grado']    ?? '');
$turno    = trim($data['turno']    ?? 'Matutino');

if (!$nombre || !$apellido || !$email || !$password || !$grado) {
    echo json_encode(['ok' => false, 'message' => 'Campos incompletos']);
    exit;
}

// Verificar si el correo ya existe
$check = $conn->prepare("SELECT id FROM usuarios WHERE email = ? LIMIT 1");
$check->bind_param("s", $email);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    echo json_encode(['ok' => false, 'message' => 'Este correo ya está registrado']);
    exit;
}

$hash = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conn->prepare("INSERT INTO usuarios (nombre, apellido, email, password_hash, grado, turno) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssss", $nombre, $apellido, $email, $hash, $grado, $turno);

if ($stmt->execute()) {
    echo json_encode(['ok' => true, 'message' => '¡Cuenta creada exitosamente!']);
} else {
    echo json_encode(['ok' => false, 'message' => 'Error al crear la cuenta']);
}