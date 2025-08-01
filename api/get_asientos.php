<?php
require_once '../config/conexion.php';
header('Content-Type: application/json; charset=utf-8');

$sala_ = isset($_POST['sala_id']) ? mysqli_real_escape_string($conexion, $_POST['sala_id']) : '';
$pelicula_id = isset($_POST['pelicula_id']) ? mysqli_real_escape_string($conexion, $_POST['pelicula_id']) : '';

if (empty($sala)) {
    echo json_encode(['error' => 'Sala no especificada']);
    exit;
}

// Por ahora solo buscamos los asientos por sala (sin verificar si están reservados por película)
$query = "SELECT id, sala, numero, estado FROM asientos WHERE sala = '$sala'";
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
    echo json_encode(['error' => 'No hay asientos registrados para la sala ' . $sala]);
    exit;
}

echo json_encode($asientos);
mysqli_close($conexion);
?>
