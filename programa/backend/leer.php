<?php
header('Content-Type: application/json; charset=utf-8');
require_once 'conexion.php';

// Seleccionamos todas las notas, ordenadas de la mÃ¡s reciente a la mÃ¡s antigua
$resultado = $conn->query("SELECT idnota, titulo, contenido, fecha FROM notas ORDER BY idnota DESC");

$notas = [];

if ($resultado) {
    while ($fila = $resultado->fetch_assoc()) {
        $notas[] = $fila;
    }
}

// Le enviamos el arreglo limpio en formato JSON a app.js
echo json_encode($notas);

$conn->close();
?>
