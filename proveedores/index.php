<?php
include '../includes/header.php';
include '../config/conexion.php';

// Consultar todos los proveedores
$query = "SELECT id, nombre, contacto FROM proveedores";
$resultado = mysqli_query($conexion, $query);
?>

<h2>Lista de Proveedores</h2>
<a href="agregar.php">Agregar Proveedor</a>
<table>
    <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Contacto</th>
        <th>Acciones</th>
    </tr>
    <?php
    // Verificar si la consulta fue exitosa
    if ($resultado) {
        while ($fila = mysqli_fetch_assoc($resultado)) { ?>
            <tr>
                <td><?php echo $fila['id']; ?></td>
                <td><?php echo $fila['nombre']; ?></td>
                <td><?php echo $fila['contacto'] ?: 'Sin contacto'; ?></td>
                <td>
                    <a href="editar.php?id=<?php echo $fila['id']; ?>">Editar</a>
                    <a href="eliminar.php?id=<?php echo $fila['id']; ?>" onclick="return confirm('¿Seguro que deseas eliminar?')">Eliminar</a>
                </td>
            </tr>
        <?php }
    } else {
        echo "<tr><td colspan='4'>Error en la consulta de proveedores: " . mysqli_error($conexion) . "</td></tr>";
    }
    ?>
</table>
<?php include '../includes/footer.php'; ?>