<?php
require_once '../config/conexion.php';
header('Content-Type: application/json; charset=utf-8');

$pelicula_id = isset($_POST['pelicula_id']) ? mysqli_real_escape_string($conexion, $_POST['pelicula_id']) : '';
$cantidad = isset($_POST['cantidad']) ? (int)$_POST['cantidad'] : 0;
$usuario_id = isset($_POST['usuario_id']) && !empty($_POST['usuario_id']) ? mysqli_real_escape_string($conexion, $_POST['usuario_id']) : NULL;
$asientos = isset($_POST['asientos']) ? $_POST['asientos'] : [];

if (empty($pelicula_id) || $cantidad <= 0 || empty($asientos) || count($asientos) != $cantidad) {
    echo json_encode(['error' => 'Datos inválidos']);
    exit;
}

// Obtener información de la película
$query = "SELECT titulo, sala, precio FROM peliculas WHERE id = '$pelicula_id'";
$result = mysqli_query($conexion, $query);
if (!$result || mysqli_num_rows($result) == 0) {
    echo json_encode(['error' => 'Película no encontrada']);
    exit;
}
$pelicula = mysqli_fetch_assoc($result);
$precio_unitario = $pelicula['precio'];
$precio_total = $precio_unitario * $cantidad;

// Verificar y reservar asientos
$factura_asientos = [];
mysqli_begin_transaction($conexion);
try {
    foreach ($asientos as $asiento_id) {
        $asiento_id = mysqli_real_escape_string($conexion, $asiento_id);
        $query = "SELECT numero, estado FROM asientos WHERE id = '$asiento_id' AND estado = 0 AND pelicula_id = '$pelicula_id'";
        $result = mysqli_query($conexion, $query);
        if (!$result || mysqli_num_rows($result) == 0) {
            throw new Exception('Asiento no disponible o no pertenece a la película');
        }
        $asiento = mysqli_fetch_assoc($result);
        $factura_asientos[] = $asiento['numero'];

        // Reservar asiento
        $query = "UPDATE asientos SET estado = 1 WHERE id = '$asiento_id'";
        if (!mysqli_query($conexion, $query)) {
            throw new Exception('Error al reservar asiento: ' . mysqli_error($conexion));
        }
    }

    // Insertar compra
    $usuario_id_sql = $usuario_id ? "'$usuario_id'" : 'NULL';
    $query = "INSERT INTO compras (usuario_id, pelicula_id, asiento_id, fecha) VALUES ";
    $values = [];
    foreach ($asientos as $asiento_id) {
        $values[] = "($usuario_id_sql, '$pelicula_id', '$asiento_id', NOW())";
    }
    $query .= implode(',', $values);
    if (!mysqli_query($conexion, $query)) {
        throw new Exception('Error al registrar compra: ' . mysqli_error($conexion));
    }

    // Confirmar transacción
    mysqli_commit($conexion);
} catch (Exception $e) {
    mysqli_rollback($conexion);
    echo json_encode(['error' => $e->getMessage()]);
    exit;
}

// Generar factura HTML
$factura_html = '
    <h3 class="text-xl font-bold mb-4">Factura de Compra</h3>
    <p><strong>Película:</strong> ' . htmlspecialchars($pelicula['titulo']) . '</p>
    <p><strong>Sala:</strong> ' . htmlspecialchars($pelicula['sala']) . '</p>
    <p><strong>Asientos:</strong> ' . implode(', ', $factura_asientos) . '</p>
    <p><strong>Precio por boleto:</strong> $' . number_format($precio_unitario, 2) . '</p>
    <p><strong>Total:</strong> $' . number_format($precio_total, 2) . '</p>
    <p><strong>Fecha:</strong> ' . date('Y-m-d H:i:s') . '</p>
    <p><strong>Usuario:</strong> ' . ($usuario_id ? 'Registrado' : 'Anónimo') . '</p>
';

echo json_encode([
    'success' => 'Compra realizada con éxito',
    'factura_html' => $factura_html
]);

mysqli_close($conexion);
?>