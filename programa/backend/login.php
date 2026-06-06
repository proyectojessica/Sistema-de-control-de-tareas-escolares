<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');

require_once 'conexion.php';

$data     = json_decode(file_get_contents('php://input'), true);
$email    = trim($data['email']    ?? '');
$password = trim($data['password'] ?? '');

if (!$email || !$password) {
    echo json_encode(['ok' => false, 'message' => 'Campos incompletos']);
    exit;
}

$stmt = $conn->prepare("SELECT id, nombre, apellido, email, password_hash FROM usuarios WHERE email = ? LIMIT 1");
$stmt->bind_param("s", $email);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if ($user && password_verify($password, $user['password_hash'])) {
    $_SESSION['logueado'] = true;
    $_SESSION['id']       = $user['id'];
    $_SESSION['nombre']   = $user['nombre'];

    // Guardar sesión en BD
    $token        = bin2hex(random_bytes(32));
    $ip           = $_SERVER['REMOTE_ADDR'] ?? null;
    $user_agent   = $_SERVER['HTTP_USER_AGENT'] ?? null;
    $fecha_expira = date('Y-m-d H:i:s', strtotime('+7 days'));

    $s = $conn->prepare("INSERT INTO sesiones (usuario_id, token, ip_address, user_agent, fecha_expira) VALUES (?, ?, ?, ?, ?)");
    $s->bind_param("issss", $user['id'], $token, $ip, $user_agent, $fecha_expira);
    $s->execute();

    setcookie('kairos_session', $token, time() + (7 * 24 * 3600), '/');

    echo json_encode([
        'ok'       => true,
        'redirect' => 'index.php',
        'usuario'  => [
            'id'     => $user['id'],
            'nombre' => $user['nombre'] . ' ' . $user['apellido'],
            'email'  => $user['email']
        ]
    ]);

} else {
    http_response_code(401);
    echo json_encode(['ok' => false, 'message' => 'Correo o contraseña incorrectos']);
}

$conn->close();
?>