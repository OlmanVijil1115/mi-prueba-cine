<?php
include '../includes/header.php';
include '../config/conexion.php';

if (!isset($_GET['id'])) {
    echo "<p>Error: ID de boleto no proporcionado.</p>";
    include '../includes/footer.php';
    exit;
}

$id = (int)$_GET['id'];
$query = "SELECT b.id, b.precio_final, b.fecha_compra, p.titulo, a.numero AS asiento, a.sala, u.nombre AS usuario, pr.nombre AS promocion 
          FROM boletos b 
          JOIN peliculas p ON b.pelicula_id = p.id 
          JOIN asientos a ON b.asiento_id = a.id 
          LEFT JOIN usuarios u ON b.usuario_id = u.id 
          LEFT JOIN facturas f ON b.id = f.boleto_id 
          LEFT JOIN promociones pr ON f.promocion_id = pr.id 
          WHERE b.id = $id";
$resultado = mysqli_query($conexion, $query);
$boleto = mysqli_fetch_assoc($resultado);

if (!$boleto) {
    echo "<p>Error: Boleto no encontrado.</p>";
    include '../includes/footer.php';
    exit;
}
?>

<style>
    .factura { max-width: 600px; margin: 20px auto; padding: 20px; border: 1px solid #ccc; background: white; }
    .factura h3 { text-align: center; }
    .factura table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
    .factura th, .factura td { border: 1px solid #ccc; padding: 8px; text-align: left; }
    @media print { header, nav, footer, .no-print { display: none; } .factura { border: none; } }
</style>

<h2>Factura de Boleto</h2>
<div class="factura">
    <h3>Factura #<?php echo $boleto['id']; ?></h3>
    <table>
        <tr><th>Película</th><td><?php echo htmlspecialchars($boleto['titulo']); ?></td></tr>
        <tr><th>Asiento</th><td><?php echo htmlspecialchars($boleto['asiento']); ?></td></tr>
        <tr><th>Sala</th><td><?php echo $boleto['sala']; ?></td></tr>
        <tr><th>Usuario</th><td><?php echo $boleto['usuario'] ?: 'Sin usuario'; ?></td></tr>
        <tr><th>Fecha</th><td><?php echo $boleto['fecha_compra']; ?></td></tr>
        <tr><th>Promoción</th><td><?php echo $boleto['promocion'] ?: 'Ninguna'; ?></td></tr>
        <tr><th>Precio Final</th><td>$<?php echo number_format($boleto['precio_final'], 2); ?></td></tr>
    </table>
    <p class="no-print">Notificación enviada a <?php echo $boleto['usuario'] ? htmlspecialchars($boleto['usuario']) . ' (' . ($boleto['usuario'] ? 'email@simulado.com' : 'sin email') . ')' : 'sin usuario'; ?>.</p>
    <button class="no-print" onclick="window.print()">Imprimir Factura</button>
</div>
<?php include '../includes/footer.php'; ?>