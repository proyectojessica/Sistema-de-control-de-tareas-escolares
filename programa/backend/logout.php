<?php
session_start();
require_once 'conexion.php';

if (isset($_COOKIE['kairos_session'])) {
    $token = $_COOKIE['kairos_session'];
    $stmt  = $conn->prepare("DELETE FROM sesiones WHERE token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    setcookie('kairos_session', '', time() - 3600, '/');
}

session_destroy();
header("Location: ../inicio.php");
exit();
?>