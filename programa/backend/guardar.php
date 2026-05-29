<?php
// 1. Configurar cabeceras antes de cualquier salida de texto
header('Content-Type: application/json; charset=utf-8');

// 2. Incluimos la conexiÃ³n obligatoria
require_once 'conexion.php';

// 3. Como los datos vienen en formato JSON desde JavaScript, los leemos asÃ­:
$json_datos = file_get_contents('php://input');
$datos = json_decode($json_datos, true);

// 4. Verificamos que realmente nos estÃ©n llegando datos
if ($datos) {
    // CORREGIDO: Leemos 'idnota' que es el nombre exacto que manda app.js
    $idnota = isset($datos['idnota']) ? $datos['idnota'] : null;
    $titulo = isset($datos['titulo']) ? trim($datos['titulo']) : '';
    $contenido = isset($datos['contenido']) ? trim($datos['contenido']) : '';

    // Si el tÃ­tulo estÃ¡ vacÃ­o, detenemos el proceso por seguridad
    if (empty($titulo)) {
        echo json_encode(["status" => "error", "message" => "El tÃ­tulo es obligatorio"]);
        exit;
    }

    // 5. LÃ“GICA DE DECISIÃ“N: Â¿Insertar nueva o Actualizar existente?
    if ($idnota === null || $idnota === '') {
        // --- GUARDAR UNA NUEVA NOTA ---
        $stmt = $conn->prepare("INSERT INTO notas (titulo, contenido) VALUES (?, ?)");
        $stmt->bind_param("ss", $titulo, $contenido);
    } else {
        // --- ACTUALIZAR UNA NOTA EXISTENTE ---
        // CORREGIDO: Cambiado 'id' por 'idnota' en el WHERE para que coincida con tu tabla
        $stmt = $conn->prepare("UPDATE notas SET titulo = ?, contenido = ? WHERE idnota = ?");
        $stmt->bind_param("ssi", $titulo, $contenido, $idnota);
    }

    // 6. Ejecutamos la consulta y respondemos a JavaScript si todo saliÃ³ bien
    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Nota guardada correctamente"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error al guardar en la base de datos: " . $conn->error]);
    }

    // Cerramos la sentencia preparada
    $stmt->close();
} else {
    echo json_encode(["status" => "error", "message" => "No se recibieron datos vÃ¡lidos"]);
}

// Cerramos la conexiÃ³n al terminar
$conn->close();
?>
