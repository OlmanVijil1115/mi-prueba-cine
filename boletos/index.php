<?php
include '../includes/header.php';
include '../config/conexion.php';

// Consultar todos los boletos
$query = "SELECT b.id, b.asiento, b.sala, p.titulo, u.nombre AS usuario 
          FROM boletos b 
          JOIN peliculas p ON b.pelicula_id = p.id 
          LEFT JOIN usuarios u ON b.usuario_id = u.id";
$resultado = mysqli_query($conexion, $query);
?>

<h2>Lista de Boletos</h2>
<table>
    <tr>
        <th>ID</th>
        <th>Película</th>
        <th>Asiento</th>
        <th>Sala</th>
        <th>Usuario</th>
        <th>Acciones</th>
    </tr>
    <?php while ($fila = mysqli_fetch_assoc($resultado)) { ?>
        <tr>
            <td><?php echo $fila['id']; ?></td>
            <td><?php echo $fila['titulo']; ?></td>
            <td><?php echo $fila['asiento']; ?></td>
            <td><?php echo $fila['sala']; ?></td>
            <td><?php echo $fila['usuario'] ?: 'Anónimo'; ?></td>
            <td>
                <a href="factura.php?id=<?php echo $fila['id']; ?>">Ver Factura</a>
            </td>
        </tr>
    <?php } ?>
</table>
<?php include '../includes/footer.php'; ?>