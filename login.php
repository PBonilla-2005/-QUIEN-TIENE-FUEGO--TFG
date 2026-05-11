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

$datos = json_decode(file_get_contents("php://input"), true);

if (!$datos) {
    die(json_encode(["error" => "No se recibieron datos."]));
}

$email = $datos['email'];
$password = $datos['password'];

// Añadimos 'rol' a la consulta
$stmt = $conexion->prepare("SELECT id, nombre, email, password, rol FROM usuarios WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows > 0) {
    $usuario_db = $resultado->fetch_assoc();
    
    if (password_verify($password, $usuario_db['password'])) {
        echo json_encode([
            "success" => true, 
            "usuario" => [
                "id" => $usuario_db['id'],
                "name" => $usuario_db['nombre'],
                "email" => $usuario_db['email'],
                "rol" => $usuario_db['rol'] // Enviamos el rol al frontend
            ]
        ]);
    } else {
        echo json_encode(["error" => "Contraseña incorrecta."]);
    }
} else {
    echo json_encode(["error" => "No existe ninguna cuenta con este correo."]);
}

$stmt->close();
$conexion->close();
?>