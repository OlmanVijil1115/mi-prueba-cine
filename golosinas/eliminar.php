<?php
include '../includes/header.php';
include '../config/conexion.php';

if (!isset($_GET['id'])) {
    echo "<p>Error: ID de golosina no proporcionado.</p>";
    include '../includes/footer.php';
    exit;
}

$id = (int)$_GET['id'];
$query = "DELETE FROM golosinas WHERE id = $id";
if (mysqli_query($conexion, $query)) {
    echo "<p>Golosina eliminada correctamente. <a href='index.php'>Volver</a></p>";
} else {
    echo "<p>Error al eliminar golosina: " . mysqli_error($conexion) . "</p>";
}
include '../includes/footer.php';
?>