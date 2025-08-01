<?php
// Iniciar la sesión
require_once '../config/conexion.php';

// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: /cine/index.php#login");
    exit;
}

$usuario_id = $_SESSION['usuario_id'];

// Obtener compras de boletos
$query = "SELECT b.id, p.titulo, a.numero AS asiento, b.precio_final, b.fecha_compra, f.id AS factura_id
          FROM boletos b 
          JOIN peliculas p ON b.pelicula_id = p.id 
          JOIN asientos a ON b.asiento_id = a.id 
          LEFT JOIN facturas f ON b.id = f.boleto_id
          WHERE b.usuario_id = '$usuario_id'";
$boletos = mysqli_query($conexion, $query);

// Obtener compras de golosinas
$query = "SELECT c.id, g.nombre, c.cantidad, c.precio_final, c.fecha_compra, f.id AS factura_id
          FROM compras_golosinas c 
          JOIN golosinas g ON c.golosina_id = g.id 
          LEFT JOIN facturas f ON c.id = f.compra_golosina_id
          WHERE c.usuario_id = '$usuario_id'";
$golosinas = mysqli_query($conexion, $query);

// Incluir el encabezado
require_once '../includes/header.php';
?>

<!-- Contenido principal -->
<div class="max-w-4xl mx-auto py-8">
    <section class="animate-fade-in">
        <h2 class="text-3xl font-bold mb-6 text-center text-gray-800 animate-pulse">Historial de Compras</h2>
        <h3 class="text-xl font-bold mb-4">Boletos</h3>
        <table class="w-full border-collapse border border-gray-300">
            <thead>
                <tr class="bg-gray-100">
                    <th class="border p-3">Película</th>
                    <th class="border p-3">Asiento</th>
                    <th class="border p-3">Precio Final</th>
                    <th class="border p-3">Fecha</th>
                    <th class="border p-3">Factura</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($boletos) == 0) { ?>
                    <tr><td colspan="5" class="border p-3 text-center">No hay compras de boletos.</td></tr>
                <?php } else { ?>
                    <?php while ($boleto = mysqli_fetch_assoc($boletos)) { ?>
                        <tr>
                            <td class="border p-3"><?php echo htmlspecialchars($boleto['titulo']); ?></td>
                            <td class="border p-3"><?php echo htmlspecialchars($boleto['asiento']); ?></td>
                            <td class="border p-3">$<?php echo number_format($boleto['precio_final'], 2); ?></td>
                            <td class="border p-3"><?php echo date('Y-m-d H:i', strtotime($boleto['fecha_compra'])); ?></td>
                            <td class="border p-3">
                                <?php if ($boleto['factura_id']) { ?>
                                    <a href="/cine/api/factura.php?factura_id=<?php echo $boleto['factura_id']; ?>" class="btn btn-primary">Descargar Factura</a>
                                <?php } else { ?>
                                    No disponible
                                <?php } ?>
                            </td>
                        </tr>
                    <?php } ?>
                <?php } ?>
            </tbody>
        </table>
        <h3 class="text-xl font-bold mb-4 mt-8">Golosinas</h3>
        <table class="w-full border-collapse border border-gray-300">
            <thead>
                <tr class="bg-gray-100">
                    <th class="border p-3">Golosina</th>
                    <th class="border p-3">Cantidad</th>
                    <th class="border p-3">Precio Final</th>
                    <th class="border p-3">Fecha</th>
                    <th class="border p-3">Factura</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($golosinas) == 0) { ?>
                    <tr><td colspan="5" class="border p-3 text-center">No hay compras de golosinas.</td></tr>
                <?php } else { ?>
                    <?php while ($golosina = mysqli_fetch_assoc($golosinas)) { ?>
                        <tr>
                            <td class="border p-3"><?php echo htmlspecialchars($golosina['nombre']); ?></td>
                            <td class="border p-3"><?php echo $golosina['cantidad']; ?></td>
                            <td class="border p-3">$<?php echo number_format($golosina['precio_final'], 2); ?></td>
                            <td class="border p-3"><?php echo date('Y-m-d H:i', strtotime($golosina['fecha_compra'])); ?></td>
                            <td class="border p-3">
                                <?php if ($golosina['factura_id']) { ?>
                                    <a href="/cine/api/factura.php?factura_id=<?php echo $golosina['factura_id']; ?>" class="btn btn-primary">Descargar Factura</a>
                                <?php } else { ?>
                                    No disponible
                                <?php } ?>
                            </td>
                        </tr>
                    <?php } ?>
                <?php } ?>
            </tbody>
        </table>
    </section>
</div>

<!-- Estilos -->
<style>
/* Animación fade-in */
.animate-fade-in {
    animation: fadeIn 1s ease-in-out;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Animación pulse */
.animate-pulse {
    animation: pulse 2s infinite;
}
@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

/* Estilos para botones */
.btn {
    display: inline-block;
    padding: 0.75rem 1.5rem;
    border-radius: 6px;
    font-weight: 500;
    text-align: center;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.btn:hover {
    transform: scale(1.05);
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}
.btn-primary {
    background-color: #3b82f6;
    color: white;
}
.btn-primary:hover {
    background-color: #2563eb;
}

/* Estilos para tabla */
.text-xl { font-size: 1.25rem; }
.text-3xl { font-size: 1.875rem; }
.font-bold { font-weight: 700; }
.mb-4 { margin-bottom: 1rem; }
.mb-6 { margin-bottom: 1.5rem; }
.mt-8 { margin-top: 2rem; }
.text-center { text-align: center; }
.border { border: 1px solid #d1d5db; }
.border-collapse { border-collapse: collapse; }
.bg-gray-100 { background-color: #f3f4f6; }
.p-3 { padding: 0.75rem; }
.w-full { width: 100%; }
</style>

<?php
// Incluir el pie de página
require_once '../includes/footer.php';

// Cerrar la conexión
mysqli_close($conexion);
?>