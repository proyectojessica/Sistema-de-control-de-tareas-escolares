<?php
$host = 'localhost';
$usuario = 'p24330050470371';
$contrasena = 'cra6371&LO';
$basedatos = 'bd24330050470371';

$conn = new mysqli($host, $usuario, $contrasena, $basedatos);

if ($conn->connect_error) {
    die("CONEXIÓN FALLIDA: " . $conn->connect_error);
}

?>