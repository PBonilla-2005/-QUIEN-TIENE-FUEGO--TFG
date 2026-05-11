<?php
header('Content-Type: application/json');

$conexion = new mysqli("localhost", "root", "", "quien_tiene_fuego");

if ($conexion->connect_error) {
    die(json_encode(["error" => "Error de conexión."]));
}

if (!isset($_GET['id'])) {
    die(json_encode(["error" => "No se ha especificado ID de producto."]));
}

$id = intval($_GET['id']);
$sql = "SELECT * FROM productos WHERE id = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows > 0) {
    echo json_encode($resultado->fetch_assoc());
} else {
    echo json_encode(["error" => "Producto no encontrado."]);
}

$stmt->close();
$conexion->close();
?>