<?php
require_once '../config/conexion.php';
header('Content-Type: application/json; charset=utf-8');

$pelicula_id = isset($_GET['pelicula_id']) ? (int)$_GET['pelicula_id'] : 0;

if ($pelicula_id <= 0) {
    echo json_encode(['error' => 'ID de película no especificado']);
    exit;
}

// Verificar que la película exista
$query = "SELECT id FROM peliculas WHERE id = '$pelicula_id'";
$result = mysqli_query($conexion, $query);
if (!$result || mysqli_num_rows($result) == 0) {
    echo json_encode(['error' => 'Película no encontrada']);
    exit;
}

$query = "SELECT id, sala, numero, estado FROM asientos WHERE pelicula_id = '$pelicula_id' AND estado = 0";
$resultado = mysqli_query($conexion, $query);
if (!$resultado) {
    echo json_encode(['error' => 'Error al cargar asientos: ' . mysqli_error($conexion)]);
    exit;
}

$asientos = [];
while ($row = mysqli_fetch_assoc($resultado)) {
    $asientos[] = [
        'id' => $row['id'],
        'sala' => $row['sala'],
        'numero' => $row['numero'],
        'estado' => $row['estado']
    ];
}

if (empty($asientos)) {
    echo json_encode(['error' => 'No hay asientos disponibles para la película']);
    exit;
}

echo json_encode($asientos);
mysqli_close($conexion);
?>