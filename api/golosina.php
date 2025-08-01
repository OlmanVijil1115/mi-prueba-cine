<?php
require_once '../config/conexion.php';
header('Content-Type: application/json; charset=utf-8');

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST' || $method === 'PUT') {
    $data = json_decode(file_get_contents('php://input'), true);
    $id = isset($data['id']) ? mysqli_real_escape_string($conexion, $data['id']) : '';
    $nombre = isset($data['nombre']) ? mysqli_real_escape_string($conexion, $data['nombre']) : '';
    $precio = isset($data['precio']) ? (float)$data['precio'] : 0;
    $stock = isset($data['stock']) ? (int)$data['stock'] : 0;
    $proveedor_id = isset($data['proveedor_id']) ? (int)$data['proveedor_id'] : 0;

    if (empty($nombre) || $precio <= 0 || $stock < 0 || $proveedor_id <= 0) {
        echo json_encode(['error' => 'Datos inválidos']);
        exit;
    }

    // Verificar proveedor
    $query = "SELECT id FROM proveedores WHERE id = '$proveedor_id'";
    $result = mysqli_query($conexion, $query);
    if (!$result || mysqli_num_rows($result) == 0) {
        echo json_encode(['error' => 'Proveedor no encontrado']);
        exit;
    }

    if ($method === 'POST') {
        $query = "INSERT INTO golosinas (nombre, precio, stock, proveedor_id) 
                  VALUES ('$nombre', '$precio', '$stock', '$proveedor_id')";
        $message = 'Golosina agregada con éxito';
    } else {
        $query = "UPDATE golosinas SET nombre = '$nombre', precio = '$precio', stock = '$stock', proveedor_id = '$proveedor_id' 
                  WHERE id = '$id'";
        $message = 'Golosina actualizada con éxito';
    }

    if (!mysqli_query($conexion, $query)) {
        echo json_encode(['error' => 'Error al guardar golosina: ' . mysqli_error($conexion)]);
        exit;
    }

    echo json_encode(['success' => $message]);
} elseif ($method === 'DELETE') {
    $data = json_decode(file_get_contents('php://input'), true);
    $id = isset($data['id']) ? mysqli_real_escape_string($conexion, $data['id']) : '';

    if (empty($id)) {
        echo json_encode(['error' => 'ID inválido']);
        exit;
    }

    $query = "DELETE FROM golosinas WHERE id = '$id'";
    if (!mysqli_query($conexion, $query)) {
        echo json_encode(['error' => 'Error al eliminar golosina: ' . mysqli_error($conexion)]);
        exit;
    }

    echo json_encode(['success' => 'Golosina eliminada']);
} else {
    echo json_encode(['error' => 'Método no soportado']);
}

mysqli_close($conexion);
?>