<?php
include '../includes/header.php';
include '../config/conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = trim($_POST['nombre']);
    $contacto = trim($_POST['contacto']);

    if (empty($nombre)) {
        echo "<p>Error: El nombre es obligatorio.</p>";
    } else {
        $query = "INSERT INTO proveedores (nombre, contacto) VALUES ('$nombre', " . ($contacto ? "'$contacto'" : 'NULL') . ")";
        if (mysqli_query($conexion, $query)) {
            echo "<p>Proveedor agregado correctamente. <a href='index.php'>Volver</a></p>";
        } else {
            echo "<p>Error al agregar proveedor: " . mysqli_error($conexion) . "</p>";
        }
    }
}
?>
<h2>Agregar Proveedor</h2>
<form method="POST" action="">
    <label>Nombre:</label>
    <input type="text" name="nombre" required>
    <label>Contacto:</label>
    <input type="text" name="contacto">
    <button type="submit">Agregar</button>
</form>
<?php include '../includes/footer.php'; ?>