<?php
// Conectar a la base de datos
require_once '../config/conexion.php';
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Método no soportado']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$titulo = mysqli_real_escape_string($conexion, $data['titulo']);
$sala = mysqli_real_escape_string($conexion, $data['sala']);
$horario = mysqli_real_escape_string($conexion, $data['horario']);
$precio = (float)$data['precio'];
$proveedor_id = (int)$data['proveedor_id'];

// Validar datos
if (empty($titulo) || empty($sala) || empty($horario) || $precio <= 0 || $proveedor_id <= 0) {
    echo json_encode(['error' => 'Datos inválidos']);
    exit;
}

// Verificar proveedor
$query = "SELECT id FROM proveedores WHERE id = '$proveedor_id'";
if (!mysqli_query($conexion, $query) || mysqli_num_rows(mysqli_query($conexion, $query)) == 0) {
    echo json_encode(['error' => 'Proveedor no encontrado']);
    exit;
}

// Insertar película
$query = "INSERT INTO peliculas (titulo, sala, horario, precio, proveedor_id) 
          VALUES ('$titulo', '$sala', '$horario', '$precio', '$proveedor_id')";
if (mysqli_query($conexion, $query)) {
    echo json_encode(['success' => 'Película agregada']);
} else {
    echo json_encode(['error' => 'Error al agregar película: ' . mysqli_error($conexion)]);
}

mysqli_close($conexion);
?>