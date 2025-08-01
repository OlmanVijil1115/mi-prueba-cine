<?php
include '../includes/header.php';
include '../config/conexion.php';

// Verificar si el usuario es administrador
if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'admin') {
    echo "<p>Acceso denegado. Solo administradores pueden ver esta página.</p>";
    include '../includes/footer.php';
    exit;
}

// Estadísticas
$num_peliculas = 0;
$num_boletos = 0;
$num_golosinas = 0;
$ventas_totales = 0;
$golosinas_data = [];

$resultado = mysqli_query($conexion, "SELECT COUNT(*) AS total FROM peliculas");
if ($resultado) $num_peliculas = mysqli_fetch_assoc($resultado)['total'];

$resultado = mysqli_query($conexion, "SELECT COUNT(*) AS total FROM boletos");
if ($resultado) $num_boletos = mysqli_fetch_assoc($resultado)['total'];

$resultado = mysqli_query($conexion, "SELECT SUM(stock) AS total FROM golosinas");
if ($resultado) $num_golosinas = mysqli_fetch_assoc($resultado)['total'] ?? 0;

$resultado = mysqli_query($conexion, "SELECT SUM(total) AS total FROM facturas");
if ($resultado) $ventas_totales = mysqli_fetch_assoc($resultado)['total'] ?? 0;

$resultado = mysqli_query($conexion, "SELECT nombre, stock FROM golosinas");
while ($row = mysqli_fetch_assoc($resultado)) {
    $golosinas_data[] = ['nombre' => $row['nombre'], 'stock' => $row['stock']];
}
?>

<h2>Panel de Administración</h2>
<div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow-lg">
    <h3 class="text-xl font-bold mb-4">Resumen del Sistema</h3>
    <p><strong>Películas disponibles:</strong> <?php echo $num_peliculas; ?></p>
    <p><strong>Boletos vendidos:</strong> <?php echo $num_boletos; ?></p>
    <p><strong>Stock total de golosinas:</strong> <?php echo $num_golosinas ? $num_golosinas : 0; ?></p>
    <p><strong>Ventas totales:</strong> $<?php echo number_format($ventas_totales, 2); ?></p>
</div>

<div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow-lg mt-6">
    <h3 class="text-xl font-bold mb-4">Stock de Golosinas</h3>
    <canvas id="golosinasChart" width="400" height="200"></canvas>
</div>

<div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow-lg mt-6">
    <h3 class="text-xl font-bold mb-4">Accesos Rápidos</h3>
    <p><a href="../peliculas/index.php" class="text-blue-500 hover:underline">Gestionar Películas</a></p>
    <p><a href="../golosinas/index.php" class="text-blue-500 hover:underline">Gestionar Golosinas</a></p>
    <p><a href="../proveedores/index.php" class="text-blue-500 hover:underline">Gestionar Proveedores</a></p>
    <p><a href="../promociones/index.php" class="text-blue-500 hover:underline">Gestionar Promociones</a></p>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('golosinasChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode(array_column($golosinas_data, 'nombre')); ?>,
            datasets: [{
                label: 'Stock de Golosinas',
                data: <?php echo json_encode(array_column($golosinas_data, 'stock')); ?>,
                backgroundColor: ['#4CAF50', '#2196F3', '#FF9800', '#F44336'],
                borderColor: ['#388E3C', '#1976D2', '#F57C00', '#D32F2F'],
                borderWidth: 1
            }]
        },
        options: {
            scales: { y: { beginAtZero: true } }
        }
    });
</script>
<?php include '../includes/footer.php'; ?>