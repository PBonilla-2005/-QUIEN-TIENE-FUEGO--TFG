<?php
header('Content-Type: application/json');

$servidor = "localhost";
$usuario  = "root";
$pass     = "";
$base_datos = "quien_tiene_fuego";

$conexion = new mysqli($servidor, $usuario, $pass, $base_datos);

if ($conexion->connect_error) {
    die(json_encode(["error" => "Error de conexión a la BBDD."]));
}

// Recibimos la cesta y el ID del usuario
$datos = json_decode(file_get_contents("php://input"), true);

if (!$datos || !isset($datos['usuario_id']) || !isset($datos['carrito'])) {
    die(json_encode(["error" => "Faltan datos para procesar el pedido."]));
}

$usuario_id = $datos['usuario_id'];
$carrito = $datos['carrito'];

// Calcular el total desde el backend (por seguridad)
$total = 0;
foreach ($carrito as $item) {
    $total += $item['price'] * $item['qty'];
}

$estado = "Completado"; // En la vida real podría ser "Pendiente de pago"

// 1. Insertamos en la tabla `pedidos`
$stmt = $conexion->prepare("INSERT INTO pedidos (usuario_id, total, estado, fecha_pedido) VALUES (?, ?, ?, NOW())");
$stmt->bind_param("ids", $usuario_id, $total, $estado); // i=int, d=double, s=string

if (!$stmt->execute()) {
    die(json_encode(["error" => "Error al crear el pedido general."]));
}

// Obtenemos el ID del pedido que se acaba de crear
$pedido_id = $stmt->insert_id;
$stmt->close();

// 2. Insertamos en `detalles_pedido` producto por producto
$stmt_detalles = $conexion->prepare("INSERT INTO detalles_pedido (pedido_id, producto_id, cantidad, precio_unitario) VALUES (?, ?, ?, ?)");

foreach ($carrito as $item) {
    $producto_id = $item['id'];
    $cantidad = $item['qty'];
    $precio_unitario = $item['price'];
    
    $stmt_detalles->bind_param("iiid", $pedido_id, $producto_id, $cantidad, $precio_unitario);
    $stmt_detalles->execute();
}

$stmt_detalles->close();
$conexion->close();

// Devolvemos el ID al usuario
echo json_encode(["success" => true, "mensaje" => "Pedido realizado", "pedido_id" => $pedido_id]);
?>