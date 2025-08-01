<?php
require_once '../config/conexion.php';
header('Content-Type: application/json; charset=utf-8');

if (!isset($_GET['funcion_id'])) {
    echo json_encode(['error' => 'Falta funcion_id']);
    exit;
}

$funcion_id = (int)$_GET['funcion_id'];
$query = "SELECT sala_id FROM funciones WHERE id = '$funcion_id'";
$resultado = mysqli_query($conexion, $query);
if ($resultado && mysqli_num_rows($resultado) > 0) {
    $funcion = mysqli_fetch_assoc($resultado);
    echo json_encode(['sala_id' => $funcion['sala_id']]);
} else {
    echo json_encode(['error' => 'Función no encontrada']);
}
mysqli_close($conexion);
?>