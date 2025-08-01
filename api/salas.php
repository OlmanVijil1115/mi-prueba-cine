<?php
require_once '../config/conexion.php';
header('Content-Type: application/json; charset=utf-8');

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    // Listar salas
    $query = "SELECT DISTINCT sala FROM asientos";
    $result = mysqli_query($conexion, $query);
    $salas = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $salas[] = ['sala' => $row['sala']];
    }
    echo json_encode($salas);
} elseif ($method === 'POST') {
    // Liberar asientos de una sala
    $data = json_decode(file_get_contents('php://input'), true);
    $sala = isset($data['sala']) ? (int)$data['sala'] : 0;

    if (!in_array($sala, [1, 2, 3, 4, 5])) {
        echo json_encode(['error' => 'Sala inválida']);
        exit;
    }

    $query = "UPDATE asientos SET estado = 0 WHERE sala = '$sala'";
    if (mysqli_query($conexion, $query)) {
        echo json_encode(['success' => "Asientos de la sala $sala liberados"]);
    } else {
        echo json_encode(['error' => 'Error al liberar asientos: ' . mysqli_error($conexion)]);
    }
} else {
    echo json_encode(['error' => 'Método no soportado']);
}

mysqli_close($conexion);
?>