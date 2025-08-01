<?php
include '../includes/header.php';
include '../config/conexion.php';

$busqueda = isset($_GET['busqueda']) ? trim($_GET['busqueda']) : '';
$query = "SELECT peliculas.id, peliculas.titulo, peliculas.sala, peliculas.horario, peliculas.precio, proveedores.nombre AS proveedor 
          FROM peliculas LEFT JOIN proveedores ON peliculas.proveedor_id = proveedores.id";
if ($busqueda) {
    $query .= " WHERE peliculas.titulo LIKE '%$busqueda%'";
}
$resultado = mysqli_query($conexion, $query);
?>
<h2>Lista de Películas</h2>
<form method="GET" action="" class="mb-4">
    <input type="text" name="busqueda" value="<?php echo htmlspecialchars($busqueda); ?>" placeholder="Buscar por título" class="w-64 p-2 border rounded-md">
    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">Buscar</button>
</form>
<a href="agregar.php" class="inline-block bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 mb-4">Agregar Película</a>
<table>
    <tr>
        <th>ID</th>
        <th>Título</th>
        <th>Sala</th>
        <th>Horario</th>
        <th>Precio</th>
        <th>Proveedor</th>
        <th>Acciones</th>
    </tr>
    <?php
    if ($resultado) {
        while ($fila = mysqli_fetch_assoc($resultado)) { ?>
            <tr>
                <td><?php echo $fila['id']; ?></td>
                <td><?php echo htmlspecialchars($fila['titulo']); ?></td>
                <td><?php echo $fila['sala']; ?></td>
                <td><?php echo $fila['horario']; ?></td>
                <td>$<?php echo number_format($fila['precio'], 2); ?></td>
                <td><?php echo $fila['proveedor'] ?: 'Sin proveedor'; ?></td>
                <td>
                    <a href="editar.php?id=<?php echo $fila['id']; ?>" class="text-blue-500 hover:underline">Editar</a>
                    <a href="eliminar.php?id=<?php echo $fila['id']; ?>" onclick="return confirm('¿Seguro que deseas eliminar?')" class="text-red-500 hover:underline">Eliminar</a>
                </td>
            </tr>
        <?php }
    } else {
        echo "<tr><td colspan='7'>Error en la consulta de películas: " . mysqli_error($conexion) . "</td></tr>";
    }
    ?>
</table>
<?php include '../includes/footer.php'; ?>