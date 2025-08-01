<?php
// Desactivar mensajes de error en la salida para evitar romper el JSON
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

// Iniciar la sesión (centralizado en config/conexion.php)
require_once '../config/conexion.php';

// Establecer el tipo de contenido como JSON
header('Content-Type: application/json; charset=utf-8');

// Verificar el método
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Método no soportado']);
    exit;
}

// Obtener datos
$data = json_decode(file_get_contents('php://input'), true);
$email = isset($data['email']) ? mysqli_real_escape_string($conexion, trim($data['email'])) : '';
$password = isset($data['contrasena']) ? $data['contrasena'] : '';

if (empty($email) || empty($password)) {
    echo json_encode(['error' => 'Email y contraseña son obligatorios']);
    exit;
}

// Verificar formato de email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['error' => 'Email inválido']);
    exit;
}

// Consultar el usuario
$query = "SELECT id, password, rol, membresia FROM usuarios WHERE email = '$email'";
$resultado = mysqli_query($conexion, $query);

if (!$resultado) {
    echo json_encode(['error' => 'Error en la consulta: ' . mysqli_error($conexion)]);
    exit;
}

if (mysqli_num_rows($resultado) == 0) {
    echo json_encode(['error' => 'Email no encontrado']);
    exit;
}

$usuario = mysqli_fetch_assoc($resultado);

// Verificar la contraseña (sin hash)
if ($password === $usuario['password']) {
    $_SESSION['usuario_id'] = $usuario['id'];
    $_SESSION['rol'] = $usuario['rol'];
    $_SESSION['membresia'] = $usuario['membresia'];
    echo json_encode(['success' => 'Inicio de sesión exitoso']);
} else {
    echo json_encode(['error' => 'Contraseña incorrecta']);
}

// Cerrar la conexión
mysqli_close($conexion);
?>