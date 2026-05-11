<?php
header('Content-Type: application/json');
$conexion = new mysqli("localhost", "root", "", "quien_tiene_fuego");

if ($conexion->connect_error) {
    die(json_encode(["error" => "Error de conexión."]));
}

$datos = json_decode(file_get_contents("php://input"), true);

if (!isset($datos['accion'])) {
    die(json_encode(["error" => "Acción no especificada."]));
}

$accion = $datos['accion'];

if ($accion === 'crear') {
    // Añadida la descripción al INSERT
    $stmt = $conexion->prepare("INSERT INTO productos (nombre, precio, imagen_url, categoria_id, stock, descripcion) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sdsiis", $datos['nombre'], $datos['precio'], $datos['imagen_url'], $datos['categoria_id'], $datos['stock'], $datos['descripcion']);
    
    if($stmt->execute()) {
        echo json_encode(["success" => true, "mensaje" => "Producto añadido."]);
    } else {
        echo json_encode(["error" => "Error al añadir: " . $conexion->error]);
    }
    $stmt->close();

} elseif ($accion === 'borrar') {
    $stmt_det = $conexion->prepare("DELETE FROM detalles_pedido WHERE producto_id = ?");
    $stmt_det->bind_param("i", $datos['id']);
    $stmt_det->execute();
    $stmt_det->close();

    $stmt = $conexion->prepare("DELETE FROM productos WHERE id = ?");
    $stmt->bind_param("i", $datos['id']);
    
    if($stmt->execute()) {
        echo json_encode(["success" => true, "mensaje" => "Producto eliminado."]);
    } else {
        echo json_encode(["error" => "Error al borrar: " . $conexion->error]);
    }
    $stmt->close();
}

$conexion->close();
?>