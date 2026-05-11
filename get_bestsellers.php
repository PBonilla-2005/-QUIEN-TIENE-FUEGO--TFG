<?php
header('Content-Type: application/json');

$servidor = "localhost";
$usuario  = "root";
$pass     = "";
$base_datos = "quien_tiene_fuego";

$conexion = new mysqli($servidor, $usuario, $pass, $base_datos);

if ($conexion->connect_error) {
    die(json_encode(["error" => "Error de conexión a la base de datos."]));
}

// Esta pedazo de consulta hace lo siguiente:
// 1. Coge los productos.
// 2. Los cruza con los detalles_pedido.
// 3. Suma la cantidad total vendida de cada uno.
// 4. Los ordena del que más ha vendido al que menos, y corta la lista en 3 (LIMIT 3).
$sql = "
    SELECT p.id, p.nombre, p.precio, p.imagen_url, COALESCE(SUM(dp.cantidad), 0) as total_vendido
    FROM productos p
    LEFT JOIN detalles_pedido dp ON p.id = dp.producto_id
    GROUP BY p.id
    ORDER BY total_vendido DESC, p.id ASC
    LIMIT 3
";

$resultado = $conexion->query($sql);

$bestsellers = [];
if ($resultado) {
    while ($fila = $resultado->fetch_assoc()) {
        $bestsellers[] = $fila;
    }
} else {
    echo json_encode(["error" => "Error al consultar los best sellers."]);
    exit();
}

echo json_encode($bestsellers);

$conexion->close();
?>