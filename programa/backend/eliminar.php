<?php
// 1. Configurar cabeceras antes de cualquier salida de texto
header('Content-Type: application/json; charset=utf-8');

// 2. Incluimos la conexiÃ³n obligatoria
require_once 'conexion.php';

// 3. Leemos el paquete JSON que nos mandarÃ¡ JavaScript
$json_datos = file_get_contents('php://input');
$datos = json_decode($json_datos, true);

// 4. Verificamos que nos llegue el ID de la nota a borrar
$id = isset($datos['idnota']) ? $datos['idnota'] : null;

if ($id !== null && $id !== '') {

    // 5. Sentencia preparada para eliminar de forma segura
    $stmt = $conn->prepare("DELETE FROM notas WHERE idnota = ?");

    // Corregido: Usamos la variable $id que es la que declaramos arriba
    $stmt->bind_param("i", $id);

    // 6. Ejecutamos y respondemos a JavaScript
    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Nota eliminada correctamente"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error al eliminar en la base de datos: " . $conn->error]);
    }

    $stmt->close();
} else {
    echo json_encode(["status" => "error", "message" => "No se proporcionÃ³ un ID vÃ¡lido"]);
}

$conn->close();
?>
