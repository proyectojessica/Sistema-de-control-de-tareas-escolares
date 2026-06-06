<?php
require_once 'backend/conexion.php';

$token = trim($_GET['token'] ?? '');
$error = '';
$exito = false;

if (!$token) {
    die('Token inválido.');
}

// Verificar token válido y no expirado
$hoy  = date('Y-m-d H:i:s');
$stmt = $conn->prepare("SELECT r.id, r.usuario_id, u.nombre FROM recuperacion_pass r JOIN usuarios u ON r.usuario_id = u.id WHERE r.token = ? AND r.fecha_expira > ? AND r.usado = 0 LIMIT 1");
$stmt->bind_param("ss", $token, $hoy);
$stmt->execute();
$rec = $stmt->get_result()->fetch_assoc();

if (!$rec) {
    die('Este enlace ya expiró o fue usado. Solicita uno nuevo.');
}

// Procesar nueva contraseña
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nueva    = trim($_POST['password'] ?? '');
    $confirma = trim($_POST['confirmar'] ?? '');

    if (strlen($nueva) < 6) {
        $error = 'La contraseña debe tener al menos 6 caracteres';
    } elseif ($nueva !== $confirma) {
        $error = 'Las contraseñas no coinciden';
    } else {
        $hash = password_hash($nueva, PASSWORD_DEFAULT);

        // Actualizar contraseña
        $upd = $conn->prepare("UPDATE usuarios SET password_hash = ? WHERE id = ?");
        $upd->bind_param("si", $hash, $rec['usuario_id']);
        $upd->execute();

        // Marcar token como usado
        $used = $conn->prepare("UPDATE recuperacion_pass SET usado = 1 WHERE id = ?");
        $used->bind_param("i", $rec['id']);
        $used->execute();

        $exito = true;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Restablecer contraseña - Kairos</title>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet"/>
<style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
  :root { --bg: #d6cfc4; --surface: #f5f1eb; --border: #c4bdb2; --accent: #1e2d40; --muted: #6b7280; --danger: #b91c1c; --success: #166534; }
  body { background: var(--bg); min-height: 100vh; display: flex; align-items: center; justify-content: center; font-family: 'DM Sans', sans-serif; }
  .card { background: var(--surface); border: 1px solid var(--border); border-radius: 24px; padding: 36px; width: min(420px, 92vw); box-shadow: 0 24px 80px rgba(30,45,64,0.18); }
  h2 { font-size: 22px; font-weight: 500; margin-bottom: 8px; color: var(--accent); }
  p.sub { font-size: 13px; color: var(--muted); margin-bottom: 24px; }
  .field { margin-bottom: 18px; }
  label { display: block; font-size: 12px; font-weight: 500; color: var(--muted); text-transform: uppercase; letter-spacing: 0.06em; margin-bottom: 7px; }
  input { width: 100%; background: #e8e2d8; border: 1.5px solid var(--border); border-radius: 11px; padding: 12px 14px; font-family: 'DM Sans', sans-serif; font-size: 14px; outline: none; }
  input:focus { border-color: var(--accent); box-shadow: 0 0 0 3px rgba(30,45,64,0.15); }
  .btn { width: 100%; background: linear-gradient(135deg, #1e2d40, #374151); border: none; border-radius: 12px; padding: 13px; font-family: 'DM Sans', sans-serif; font-size: 15px; font-weight: 500; color: #fff; cursor: pointer; margin-top: 8px; }
  .btn:hover { opacity: 0.9; }
  .error { background: #fee2e2; border: 1px solid #fca5a5; border-radius: 10px; padding: 10px 14px; font-size: 13px; color: var(--danger); margin-bottom: 16px; }
  .exito { text-align: center; }
  .exito h2 { color: var(--success); }
  .exito a { color: var(--accent); font-size: 14px; display: inline-block; margin-top: 16px; }
</style>
</head>
<body>
<div class="card">
<?php if ($exito): ?>
  <div class="exito">
    <h2>✅ Contraseña actualizada</h2>
    <p style="color:var(--muted);font-size:13px;margin-top:8px;">Ya puedes iniciar sesión con tu nueva contraseña.</p>
    <a href="inicio.php">← Ir al inicio de sesión</a>
  </div>
<?php else: ?>
  <h2>Nueva contraseña</h2>
  <p class="sub">Hola <?= htmlspecialchars($rec['nombre']) ?>, escribe tu nueva contraseña.</p>
  <?php if ($error): ?>
    <div class="error"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>
  <form method="POST">
    <div class="field">
      <label>Nueva contraseña</label>
      <input type="password" name="password" placeholder="Mínimo 6 caracteres" required>
    </div>
    <div class="field">
      <label>Confirmar contraseña</label>
      <input type="password" name="confirmar" placeholder="Repite la contraseña" required>
    </div>
    <button type="submit" class="btn">Guardar nueva contraseña →</button>
  </form>
<?php endif; ?>
</div>
</body>
</html>