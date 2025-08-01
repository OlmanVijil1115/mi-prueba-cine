<?php
// Desactivar mensajes de error en la salida para evitar romper el JSON
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

// Iniciar la sesión (centralizado en config/conexion.php)
require_once '../config/conexion.php';

if (isset($_SESSION['usuario_id'])) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['error' => 'Ya estás autenticado']);
    exit;
}

// Establecer el tipo de contenido como JSON
header('Content-Type: application/json; charset=utf-8');

// Verificar el método
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Método no soportado']);
    exit;
}

// Obtener datos
$data = json_decode(file_get_contents('php://input'), true);
$nombre = isset($data['nombre']) ? mysqli_real_escape_string($conexion, trim($data['nombre'])) : '';
$email = isset($data['email']) ? mysqli_real_escape_string($conexion, trim($data['email'])) : '';
$password = isset($data['contrasena']) ? $data['contrasena'] : '';
$membresia = isset($data['membresia']) ? mysqli_real_escape_string($conexion, $data['membresia']) : 'estandar';

// Validar datos
if (empty($nombre) || empty($email) || empty($password) || !in_array($membresia, ['estandar', 'premium'])) {
    echo json_encode(['error' => 'Datos inválidos']);
    exit;
}

// Verificar formato de email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['error' => 'Email inválido']);
    exit;
}

// Verificar si el email ya está registrado
$query = "SELECT id FROM usuarios WHERE email = '$email'";
$resultado = mysqli_query($conexion, $query);
if (!$resultado) {
    echo json_encode(['error' => 'Error en la consulta: ' . mysqli_error($conexion)]);
    exit;
}
if (mysqli_num_rows($resultado) > 0) {
    echo json_encode(['error' => 'El email ya está registrado']);
    exit;
}

// Insertar el usuario (sin hash)
$query = "INSERT INTO usuarios (nombre, email, password, rol, membresia) VALUES ('$nombre', '$email', '$password', 'cliente', '$membresia')";
if (mysqli_query($conexion, $query)) {
    echo json_encode(['success' => 'Registro exitoso']);
} else {
    echo json_encode(['error' => 'Error al registrar: ' . mysqli_error($conexion)]);
}

// Cerrar la conexión
mysqli_close($conexion);
?>