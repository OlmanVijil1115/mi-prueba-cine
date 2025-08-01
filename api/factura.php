<?php
// Conectar a la base de datos
require_once '../config/conexion.php';
header('Content-Type: text/html; charset=utf-8');

if (!isset($_GET['factura_id'])) {
    echo 'Falta factura_id';
    exit;
}

$factura_id = (int)$_GET['factura_id'];

// Obtener datos de la factura
$query = "SELECT f.id, f.total, f.fecha, b.pelicula_id, b.asiento_id, p.titulo, p.sala, a.numero 
          FROM facturas f 
          JOIN boletos b ON f.boleto_id = b.id 
          JOIN peliculas p ON b.pelicula_id = p.id 
          JOIN asientos a ON b.asiento_id = a.id 
          WHERE f.id = '$factura_id'";
$resultado = mysqli_query($conexion, $query);
if (!$resultado || mysqli_num_rows($resultado) == 0) {
    echo 'Factura no encontrada';
    exit;
}

$factura = mysqli_fetch_assoc($resultado);
?>

<div class="factura-container">
    <div class="factura-header">
        <h1 class="factura-title">Cine XYZ</h1>
        <p class="factura-subtitle">¡Gracias por tu compra!</p>
    </div>
    <div class="factura-content">
        <h2 class="factura-asiento">Asiento: <?php echo htmlspecialchars($factura['numero']); ?></h2>
        <p class="factura-sala">Sala: <?php echo htmlspecialchars($factura['sala']); ?></p>
        <p class="factura-detalle">Película: <?php echo htmlspecialchars($factura['titulo']); ?></p>
        <p class="factura-detalle">Fecha: <?php echo date('Y-m-d H:i', strtotime($factura['fecha'])); ?></p>
        <p class="factura-detalle">Total: $<?php echo number_format($factura['total'], 2); ?></p>
    </div>
    <div class="factura-footer">
        <p>Cine XYZ - Disfruta la magia del cine</p>
        <p>Av. Principal 123, Ciudad XYZ</p>
    </div>
</div>

<style>
.factura-container {
    font-family: Arial, sans-serif;
    text-align: center;
    padding: 20px;
    background: linear-gradient(to bottom, #ffffff, #e0f2fe);
    border: 2px solid #3b82f6;
    border-radius: 10px;
    max-width: 500px;
    margin: 0 auto;
}
.factura-header {
    border-bottom: 1px solid #d1d5db;
    padding-bottom: 10px;
    margin-bottom: 20px;
}
.factura-title {
    font-size: 2.5rem;
    color: #1f2937;
    margin: 0;
}
.factura-subtitle {
    font-size: 1.2rem;
    color: #4b5563;
}
.factura-asiento {
    font-size: 3rem;
    font-weight: bold;
    color: #3b82f6;
    margin: 20px 0;
}
.factura-sala {
    font-size: 1rem;
    color: #6b7280;
    margin: 10px 0;
}
.factura-detalle {
    font-size: 1.1rem;
    color: #4b5563;
    margin: 5px 0;
}
.factura-footer {
    border-top: 1px solid #d1d5db;
    padding-top: 10px;
    margin-top: 20px;
    font-size: 0.9rem;
    color: #6b7280;
}
</style>

<?php
mysqli_close($conexion);
?>