<?php
include '../includes/header.php';
include '../config/conexion.php';

$query = "SELECT id, nombre, descripcion, descuento, fecha_inicio, fecha_fin FROM promociones";
$resultado = mysqli_query($conexion, $query);
?>
<h2>Promociones</h2>
<a href="agregar.php" class="inline-block bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 mb-4">Agregar Promoción</a>
<table>
    <tr>
        <th>Nombre</th>
        <th>Descripción</th>
        <th>Descuento</th>
        <th>Fechas</th>
        <th>Acciones</th>
    </tr>
    <?php
    if ($resultado) {
        while ($fila = mysqli_fetch_assoc($resultado)) { ?>
            <tr>
                <td><?php echo htmlspecialchars($fila['nombre']); ?></td>
                <td><?php echo htmlspecialchars($fila['descripcion']); ?></td>
                <td><?php echo ($fila['descuento'] * 100) . '%'; ?></td>
                <td><?php echo $fila['fecha_inicio'] . ' a ' . $fila['fecha_fin']; ?></td>
                <td>
                    <a href="editar.php?id=<?php echo $fila['id']; ?>" class="text-blue-500 hover:underline">Editar</a>
                    <a href="eliminar.php?id=<?php echo $fila['id']; ?>" onclick="return confirm('¿Seguro que deseas eliminar?')" class="text-red-500 hover:underline">Eliminar</a>
                </td>
            </tr>
        <?php }
    } else {
        echo "<tr><td colspan='5'>Error en la consulta de promociones: " . mysqli_error($conexion) . "</td></tr>";
    }
    ?>
</table>
<?php include '../includes/footer.php'; ?>