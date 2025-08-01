<?php
include '../includes/header.php';
include '../config/conexion.php';

// Consultar todas las golosinas
$query = "SELECT golosinas.id, golosinas.nombre, golosinas.precio, golosinas.stock, proveedores.nombre AS proveedor 
          FROM golosinas LEFT JOIN proveedores ON golosinas.proveedor_id = proveedores.id";
$resultado = mysqli_query($conexion, $query);
?>

<h2>Inventario de Golosinas</h2>
<a href="agregar.php">Agregar Golosina</a>
<table>
    <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Precio</th>
        <th>Stock</th>
        <th>Proveedor</th>
        <th>Acciones</th>
    </tr>
    <?php
    // Verificar si la consulta fue exitosa
    if ($resultado) {
        while ($fila = mysqli_fetch_assoc($resultado)) { ?>
            <tr>
                <td><?php echo $fila['id']; ?></td>
                <td><?php echo $fila['nombre']; ?></td>
                <td>$<?php echo $fila['precio']; ?></td>
                <td><?php echo $fila['stock']; ?></td>
                <td><?php echo $fila['proveedor'] ?: 'Sin proveedor'; ?></td>
                <td>
                    <a href="editar.php?id=<?php echo $fila['id']; ?>">Editar</a>
                    <a href="eliminar.php?id=<?php echo $fila['id']; ?>" onclick="return confirm('¿Seguro que deseas eliminar?')">Eliminar</a>
                </td>
            </tr>
        <?php }
    } else {
        echo "<tr><td colspan='6'>Error en la consulta de golosinas: " . mysqli_error($conexion) . "</td></tr>";
    }
    ?>
</table>
<?php include '../includes/footer.php'; ?>