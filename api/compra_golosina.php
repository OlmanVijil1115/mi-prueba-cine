<?php
require_once '../config/conexion.php';
header('Content-Type: application/json; charset=utf-8');

$golosina_id = isset($_POST['golosina_id']) ? mysqli_real_escape_string($conexion, $_POST['golosina_id']) : '';
$cantidad = isset($_POST['cantidad']) ? (int)$_POST['cantidad'] : 0;
$precio = isset($_POST['precio']) ? (float)$_POST['precio'] : 0;
$usuario_id = isset($_POST['usuario_id']) && !empty($_POST['usuario_id']) ? mysqli_real_escape_string($conexion, $_POST['usuario_id']) : NULL;

if (empty($golosina_id) || $cantidad <= 0 || $precio <= 0) {
    echo json_encode(['error' => 'Datos inválidos']);
    exit;
}

// Verificar golosina y stock
$query = "SELECT nombre, stock, precio FROM golosinas WHERE id = '$golosina_id' AND stock >= $cantidad";
$result = mysqli_query($conexion, $query);
if (!$result || mysqli_num_rows($result) == 0) {
    echo json_encode(['error' => 'Golosina no disponible o stock insuficiente']);
    exit;
}
$golosina = mysqli_fetch_assoc($result);
$precio_total = $precio * $cantidad;

// Actualizar stock
$query = "UPDATE golosinas SET stock = stock - $cantidad WHERE id = '$golosina_id'";
if (!mysqli_query($conexion, $query)) {
    echo json_encode(['error' => 'Error al actualizar stock: ' . mysqli_error($conexion)]);
    exit;
}

// Insertar compra
$usuario_id_sql = $usuario_id ? "'$usuario_id'" : 'NULL';
$query = "INSERT INTO compras_golosinas (golosina_id, usuario_id, cantidad, precio_final, fecha_compra) 
          VALUES ('$golosina_id', $usuario_id_sql, $cantidad, '$precio_total', NOW())";
if (!mysqli_query($conexion, $query)) {
    echo json_encode(['error' => 'Error al registrar compra: ' . mysqli_error($conexion)]);
    exit;
}

// Generar factura HTML
$factura_html = '
    <h3 class="text-xl font-bold mb-4">Factura de Compra</h3>
    <p><strong>Golosina:</strong> ' . htmlspecialchars($golosina['nombre']) . '</p>
    <p><strong>Cantidad:</strong> ' . $cantidad . '</p>
    <p><strong>Precio por unidad:</strong> $' . number_format($precio, 2) . '</p>
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