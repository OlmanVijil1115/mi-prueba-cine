<?php
include '../includes/header.php';
include '../config/conexion.php';

if (!isset($_GET['id'])) {
    echo "<p>Error: ID de proveedor no proporcionado.</p>";
    include '../includes/footer.php';
    exit;
}

$id = (int)$_GET['id'];
$query = "DELETE FROM proveedores WHERE id = $id";
if (mysqli_query($conexion, $query)) {
    echo "<p>Proveedor eliminado correctamente. <a href='index.php'>Volver</a></p>";
} else {
    echo "<p>Error al eliminar proveedor: " . mysqli_error($conexion) . "</p>";
}
include '../includes/footer.php';
?>