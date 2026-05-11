<?php
header('Content-Type: application/json');

$servidor = "localhost";
$usuario  = "root";
$pass     = "";
$base_datos = "quien_tiene_fuego";

$conexion = new mysqli($servidor, $usuario, $pass, $base_datos);

if ($conexion->connect_error) {
    die(json_encode(["error" => "Error de conexión."]));
}

// Le pasaremos el id por la URL: get_pedidos.php?usuario_id=3
$usuario_id = isset($_GET['usuario_id']) ? intval($_GET['usuario_id']) : 0;

if ($usuario_id === 0) {
    die(json_encode(["error" => "ID de usuario inválido."]));
}

// 1. Buscamos sus pedidos
$stmt = $conexion->prepare("SELECT id, total, estado, fecha_pedido FROM pedidos WHERE usuario_id = ? ORDER BY fecha_pedido DESC");
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$resultado = $stmt->get_result();

$pedidos = [];
while ($fila = $resultado->fetch_assoc()) {
    $pedido_id = $fila['id'];
    
    // 2. Buscamos los productos de ESTE pedido en concreto haciendo un JOIN con la tabla productos
    $stmt_det = $conexion->prepare("
        SELECT dp.cantidad, dp.precio_unitario, p.id as producto_id, p.nombre, p.imagen_url
        FROM detalles_pedido dp
        JOIN productos p ON dp.producto_id = p.id
        WHERE dp.pedido_id = ?
    ");
    $stmt_det->bind_param("i", $pedido_id);
    $stmt_det->execute();
    $res_det = $stmt_det->get_result();
    
    $detalles = [];
    while ($d = $res_det->fetch_assoc()) {
        $detalles[] = $d;
    }
    
    // Metemos los detalles dentro del pedido
    $fila['detalles'] = $detalles;
    $pedidos[] = $fila;
    
    $stmt_det->close();
}

echo json_encode($pedidos);

$stmt->close();
$conexion->close();
?>