<?php
header('Content-Type: application/json');
$conexion = new mysqli("localhost", "root", "", "quien_tiene_fuego");

if ($conexion->connect_error) {
    die(json_encode(["error" => "Error de conexión."]));
}

$sql = "SELECT p.id, p.total, p.estado, p.fecha_pedido, u.nombre, u.email 
        FROM pedidos p 
        JOIN usuarios u ON p.usuario_id = u.id 
        ORDER BY p.fecha_pedido DESC";

$resultado = $conexion->query($sql);
$pedidos = [];

while ($fila = $resultado->fetch_assoc()) {
    $pedidos[] = $fila;
}

echo json_encode($pedidos);
$conexion->close();
?>