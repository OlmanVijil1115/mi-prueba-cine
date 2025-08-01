<?php
require_once '../config/conexion.php';
header('Content-Type: application/json; charset=utf-8');

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST' || $method === 'PUT') {
    $data = json_decode(file_get_contents('php://input'), true);
    $id = isset($data['id']) ? mysqli_real_escape_string($conexion, $data['id']) : '';
    $titulo = isset($data['titulo']) ? mysqli_real_escape_string($conexion, $data['titulo']) : '';
    $sala = isset($data['sala']) ? (int)$data['sala'] : 0;
    $horario = isset($data['horario']) ? mysqli_real_escape_string($conexion, $data['horario']) : '';
    $precio = isset($data['precio']) ? (float)$data['precio'] : 0;
    $proveedor_id = isset($data['proveedor_id']) ? (int)$data['proveedor_id'] : 0;

    if (empty($titulo) || !in_array($sala, [1, 2, 3, 4, 5]) || empty($horario) || $precio <= 0 || $proveedor_id <= 0) {
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
        $query = "INSERT INTO peliculas (titulo, sala, horario, precio, proveedor_id) 
                  VALUES ('$titulo', '$sala', '$horario', '$precio', '$proveedor_id')";
        $message = 'Película agregada con éxito';
    } else {
        $query = "UPDATE peliculas SET titulo = '$titulo', sala = '$sala', horario = '$horario', 
                  precio = '$precio', proveedor_id = '$proveedor_id' WHERE id = '$id'";
        $message = 'Película actualizada con éxito';
    }

    if (!mysqli_query($conexion, $query)) {
        echo json_encode(['error' => 'Error al guardar película: ' . mysqli_error($conexion)]);
        exit;
    }

    // Asociar asientos a la nueva película (si es POST)
    if ($method === 'POST') {
        $new_id = mysqli_insert_id($conexion);
        $query = "UPDATE asientos SET pelicula_id = '$new_id', estado = 0 WHERE sala = '$sala'";
        if (!mysqli_query($conexion, $query)) {
            echo json_encode(['error' => 'Error al asociar asientos: ' . mysqli_error($conexion)]);
            exit;
        }
    }

    echo json_encode(['success' => $message]);
} elseif ($method === 'DELETE') {
    $data = json_decode(file_get_contents('php://input'), true);
    $id = isset($data['id']) ? mysqli_real_escape_string($conexion, $data['id']) : '';

    if (empty($id)) {
        echo json_encode(['error' => 'ID inválido']);
        exit;
    }

    // Iniciar transacción
    mysqli_begin_transaction($conexion);

    try {
        // Obtener la sala de la película a eliminar
        $query = "SELECT sala FROM peliculas WHERE id = '$id'";
        $result = mysqli_query($conexion, $query);
        if (!$result || !($row = mysqli_fetch_assoc($result))) {
            throw new Exception('Película no encontrada');
        }
        $sala = $row['sala'];

        // Reiniciar todos los asientos de la sala: desasociar y poner disponibles
        $query = "UPDATE asientos SET estado = 0, pelicula_id = NULL WHERE sala = '$sala'";
        if (!mysqli_query($conexion, $query)) {
            throw new Exception('Error al reiniciar asientos: ' . mysqli_error($conexion));
        }

        // Eliminar la película
        $query = "DELETE FROM peliculas WHERE id = '$id'";
        if (!mysqli_query($conexion, $query)) {
            throw new Exception('Error al eliminar película: ' . mysqli_error($conexion));
        }

        // Confirmar transacción
        mysqli_commit($conexion);
        echo json_encode(['success' => 'Película eliminada']);
    } catch (Exception $e) {
        // Revertir transacción en caso de error
        mysqli_rollback($conexion);
        echo json_encode(['error' => 'Error al eliminar película: ' . $e->getMessage()]);
    }
} elseif ($method === 'GET') {
    $query = "SELECT p.id, p.titulo, p.sala, p.horario, p.precio, pr.nombre AS proveedor 
              FROM peliculas p 
              JOIN proveedores pr ON p.proveedor_id = pr.id";
    $result = mysqli_query($conexion, $query);
    $peliculas = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $peliculas[] = $row;
    }
    echo json_encode($peliculas);
} else {
    echo json_encode(['error' => 'Método no soportado']);
}

mysqli_close($conexion);
?>