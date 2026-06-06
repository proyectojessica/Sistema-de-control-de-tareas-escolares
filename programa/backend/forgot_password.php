<?php
header('Content-Type: application/json; charset=utf-8');
session_start();
require_once 'conexion.php';

$data  = json_decode(file_get_contents('php://input'), true);
$email = trim($data['email'] ?? '');

if (!$email) {
    echo json_encode(['ok' => false, 'message' => 'Escribe tu correo']);
    exit;
}

// Verificar que el correo existe
$stmt = $conn->prepare("SELECT id, nombre FROM usuarios WHERE email = ? LIMIT 1");
$stmt->bind_param("s", $email);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
    echo json_encode(['ok' => false, 'message' => 'No encontramos ese correo']);
    exit;
}

// Generar token
$token        = bin2hex(random_bytes(32));
$fecha_expira = date('Y-m-d H:i:s', strtotime('+1 hour'));

// Borrar tokens anteriores
$del = $conn->prepare("DELETE FROM recuperacion_pass WHERE usuario_id = ?");
$del->bind_param("i", $user['id']);
$del->execute();

// Guardar nuevo token
$ins = $conn->prepare("INSERT INTO recuperacion_pass (usuario_id, token, fecha_expira) VALUES (?, ?, ?)");
$ins->bind_param("iss", $user['id'], $token, $fecha_expira);
$ins->execute();

$link = "http://186.96.178.180/alu/p24330050470371/proyecto1/restablecer.php?token=$token";

// Devolver el link directo SIN intentar enviar email
echo json_encode([
    'ok'      => true,
    'message' => '¡Enlace generado!',
    'link'    => $link
]);

$conn->close();
?>