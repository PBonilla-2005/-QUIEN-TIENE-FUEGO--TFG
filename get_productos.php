<?php
header('Content-Type: application/json');

$servidor = "localhost";
$usuario  = "root";
$pass     = "";
$base_datos = "quien_tiene_fuego";

$conexion = new mysqli($servidor, $usuario, $pass, $base_datos);

if ($conexion->connect_error) {
    die(json_encode(["error" => "Fallo de conexión a la base de datos."]));
}

$categoria = isset($_GET['categoria']) ? $_GET['categoria'] : 'all';

// Aquí está la clave: seleccionamos el stock también
if ($categoria === 'all') {
    $sql = "SELECT id, nombre, precio, imagen_url, categoria_id, stock FROM productos";
    $resultado = $conexion->query($sql);
} else {
    $sql = "SELECT id, nombre, precio, imagen_url, categoria_id, stock FROM productos WHERE categoria_id = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $categoria);
    $stmt->execute();
    $resultado = $stmt->get_result();
}

$productos = [];
if ($resultado) {
    while ($fila = $resultado->fetch_assoc()) {
        $productos[] = $fila;
    }
} else {
    echo json_encode(["error" => "Error al consultar los productos."]);
    exit();
}

echo json_encode($productos);

if (isset($stmt)) {
    $stmt->close();
}
$conexion->close();
?>