<?php
require_once '../config/conexion.php';
header('Content-Type: application/json; charset=utf-8');

$method = $_SERVER['REQUEST_METHOD'];
if ($method !== 'POST') {
    echo json_encode(['error' => 'Método no soportado']);
    exit;
}

$nombre = isset($_POST['nombre']) ? mysqli_real_escape_string($conexion, $_POST['nombre']) : '';
$email = isset($_POST['email']) ? mysqli_real_escape_string($conexion, $_POST['email']) : '';
$password = isset($_POST['password']) ? mysqli_real_escape_string($conexion, $_POST['password']) : '';
$membresia = isset($_POST['membresia']) && in_array($_POST['membresia'], ['basica', 'premium']) ? $_POST['membresia'] : 'basica';

if (empty($nombre) || empty($email) || empty($password)) {
    echo json_encode(['error' => 'Todos los campos son obligatorios']);
    exit;
}

// Verificar si el correo ya existe
$query = "SELECT id FROM usuarios WHERE email = '$email'";
$result = mysqli_query($conexion, $query);
if (!$result) {
    echo json_encode(['error' => 'Error en la consulta: ' . mysqli_error($conexion)]);
    exit;
}
if (mysqli_num_rows($result) > 0) {
    echo json_encode(['error' => 'El correo ya está registrado']);
    exit;
}

// Insertar usuario
$query = "INSERT INTO usuarios (nombre, email, password, membresia, rol, fecha_registro) 
          VALUES ('$nombre', '$email', '$password', '$membresia', 'usuario', NOW())";
if (!mysqli_query($conexion, $query)) {
    echo json_encode(['error' => 'Error al registrar usuario: ' . mysqli_error($conexion)]);
    exit;
}

echo json_encode(['success' => 'Usuario registrado con éxito']);
mysqli_close($conexion);
?>