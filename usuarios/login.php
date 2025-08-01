<?php
// Iniciar la sesión
require_once '../config/conexion.php';
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Método no soportado']);
    exit;
}

// Obtener datos del formulario
$email = mysqli_real_escape_string($conexion, $_POST['email']);
$password = $_POST['password'];

// Validar entrada
if (empty($email) || empty($password)) {
    echo json_encode(['error' => 'Correo y contraseña son obligatorios']);
    exit;
}

// Verificar usuario
$query = "SELECT id, nombre, password, membresia, rol FROM usuarios WHERE email = '$email'";
$resultado = mysqli_query($conexion, $query);
if (!$resultado || mysqli_num_rows($resultado) == 0) {
    echo json_encode(['error' => 'Usuario no encontrado']);
    exit;
}

$usuario = mysqli_fetch_assoc($resultado);
if ($password !== $usuario['password']) {
    echo json_encode(['error' => 'Contraseña incorrecta']);
    exit;
}

// Iniciar sesión
$_SESSION['usuario_id'] = $usuario['id'];
$_SESSION['nombre'] = $usuario['nombre'];
$_SESSION['membresia'] = $usuario['membresia'];
$_SESSION['rol'] = $usuario['rol'];

echo json_encode(['success' => 'Inicio de sesión exitoso']);
mysqli_close($conexion);
?>