<?php
include '../includes/header.php';
include '../config/conexion.php';

// Verificar si el usuario está logueado (opcional, ya que permitimos compras sin registro)
$usuario_id = isset($_SESSION['usuario_id']) ? (int)$_SESSION['usuario_id'] : null;

// Consultar facturas de boletos
$query_boletos = "SELECT f.id, f.total, f.fecha, p.titulo, a.numero AS asiento, a.sala, pr.nombre AS promocion 
                  FROM facturas f 
                  JOIN boletos b ON f.boleto_id = b.id 
                  JOIN peliculas p ON b.pelicula_id = p.id 
                  JOIN asientos a ON b.asiento_id = a.id 
                  LEFT JOIN promociones pr ON f.promocion_id = pr.id 
                  WHERE f.boleto_id IS NOT NULL" . ($usuario_id ? " AND f.usuario_id = $usuario_id" : "");
$resultado_boletos = mysqli_query($conexion, $query_boletos);
if (!$resultado_boletos) {
    echo "<p>Error en la consulta de boletos: " . mysqli_error($conexion) . "</p>";
}

// Consultar facturas de golosinas
$query_golosinas = "SELECT f.id, f.total, f.fecha, g.nombre AS golosina, cg.cantidad, pr.nombre AS promocion 
                    FROM facturas f 
                    JOIN compras_golosinas cg ON f.compra_golosina_id = cg.id 
                    JOIN golosinas g ON cg.golosina_id = g.id 
                    LEFT JOIN promociones pr ON f.promocion_id = pr.id 
                    WHERE f.compra_golosina_id IS NOT NULL" . ($usuario_id ? " AND f.usuario_id = $usuario_id" : "");
$resultado_golosinas = mysqli_query($conexion, $query_golosinas);
if (!$resultado_golosinas) {
    echo "<p>Error en la consulta de golosinas: " . mysqli_error($conexion) . "</p>";
}
?>

<h2>Historial de Compras</h2>
<h3>Boletos</h3>
<table>
    <tr>
        <th>ID Factura</th>
        <th>Película</th>
        <th>Asiento</th>
        <th>Sala</th>
        <th>Promoción</th>
        <th>Total</th>
        <th>Fecha</th>
    </tr>
    <?php
    if ($resultado_boletos && mysqli_num_rows($resultado_boletos) > 0) {
        while ($fila = mysqli_fetch_assoc($resultado_boletos)) { ?>
            <tr>
                <td><?php echo $fila['id']; ?></td>
                <td><?php echo htmlspecialchars($fila['titulo']); ?></td>
                <td><?php echo htmlspecialchars($fila['asiento']); ?></td>
                <td><?php echo $fila['sala']; ?></td>
                <td><?php echo $fila['promocion'] ?: 'Ninguna'; ?></td>
                <td>$<?php echo number_format($fila['total'], 2); ?></td>
                <td><?php echo $fila['fecha']; ?></td>
            </tr>
        <?php }
    } else {
        echo "<tr><td colspan='7'>No hay compras de boletos.</td></tr>";
    }
    ?>
</table>

<h3>Golosinas</h3>
<table>
    <tr>
        <th>ID Factura</th>
        <th>Golosina</th>
        <th>Cantidad</th>
        <th>Promoción</th>
        <th>Total</th>
        <th>Fecha</th>
    </tr>
    <?php
    if ($resultado_golosinas && mysqli_num_rows($resultado_golosinas) > 0) {
        while ($fila = mysqli_fetch_assoc($resultado_golosinas)) { ?>
            <tr>
                <td><?php echo $fila['id']; ?></td>
                <td><?php echo htmlspecialchars($fila['golosina']); ?></td>
                <td><?php echo $fila['cantidad']; ?></td>
                <td><?php echo $fila['promocion'] ?: 'Ninguna'; ?></td>
                <td>$<?php echo number_format($fila['total'], 2); ?></td>
                <td><?php echo $fila['fecha']; ?></td>
            </tr>
        <?php }
    } else {
        echo "<tr><td colspan='6'>No hay compras de golosinas.</td></tr>";
    }
    ?>
</table>
<?php include '../includes/footer.php'; ?>