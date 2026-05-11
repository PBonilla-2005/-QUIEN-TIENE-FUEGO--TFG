<?php
// Decimos que la respuesta será JSON
header('Content-Type: application/json');

// 1. Datos de conexión
$servidor = "localhost";
$usuario  = "root";
$pass     = "";
$base_datos = "quien_tiene_fuego";

$conexion = new mysqli($servidor, $usuario, $pass, $base_datos);

if ($conexion->connect_error) {
    die(json_encode(["error" => "Fallo de conexión a la base de datos."]));
}

// 2. Recibir los datos del frontend que nos llegan por fetch (en formato JSON)
$datos = json_decode(file_get_contents("php://input"), true);

if (!$datos) {
    die(json_encode(["error" => "No se recibieron datos."]));
}

$nombre = $datos['nombre'];
$email = $datos['email'];
$password = $datos['password'];
$direccion = ""; // De momento la dejamos vacía, ya que el form de registro no la pide

// 3. Comprobar si el email ya existe en la base de datos
$stmt_check = $conexion->prepare("SELECT id FROM usuarios WHERE email = ?");
$stmt_check->bind_param("s", $email); // La "s" significa que el parámetro es un String
$stmt_check->execute();
$stmt_check->store_result();

if ($stmt_check->num_rows > 0) {
    echo json_encode(["error" => "Este correo ya está registrado."]);
    $stmt_check->close();
    $conexion->close();
    exit();
}
$stmt_check->close();

// 4. Encriptar la contraseña (¡Súper importante para el TFG!)
$password_hasheada = password_hash($password, PASSWORD_DEFAULT);

// 5. Insertar el nuevo usuario en la tabla 'usuarios'
// Usamos NOW() de SQL para meter la fecha y hora actuales en 'fecha_registro'
$stmt_insert = $conexion->prepare("INSERT INTO usuarios (nombre, email, password, direccion, fecha_registro) VALUES (?, ?, ?, ?, NOW())");
$stmt_insert->bind_param("ssss", $nombre, $email, $password_hasheada, $direccion);

if ($stmt_insert->execute()) {
    echo json_encode(["success" => true, "mensaje" => "Usuario registrado correctamente."]);
} else {
    echo json_encode(["error" => "Error al crear la cuenta: " . $conexion->error]);
}

$stmt_insert->close();
$conexion->close();
?>