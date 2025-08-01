<?php
include '../includes/header.php';
include '../config/conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = trim($_POST['nombre']);
    $precio = (float)$_POST['precio'];
    $stock = (int)$_POST['stock'];
    $proveedor_id = !empty($_POST['proveedor_id']) ? (int)$_POST['proveedor_id'] : null;

    // Validaciones
    if (empty($nombre) || $precio <= 0 || $stock < 0) {
        echo "<p>Error: Todos los campos son obligatorios y deben ser válidos.</p>";
    } else {
        $query = "INSERT INTO golosinas (nombre, precio, stock, proveedor_id) VALUES ('$nombre', $precio, $stock, " . ($proveedor_id ? $proveedor_id : 'NULL') . ")";
        if (mysqli_query($conexion, $query)) {
            echo "<p>Golosina agregada correctamente. <a href='index.php'>Volver</a></p>";
        } else {
            echo "<p>Error al agregar golosina: " . mysqli_error($conexion) . "</p>";
        }
    }
}

$proveedores = mysqli_query($conexion, "SELECT id, nombre FROM proveedores");
?>
<h2>Agregar Golosina</h2>
<form method="POST" action="">
    <label>Nombre:</label>
    <input type="text" name="nombre" required>
    <label>Precio:</label>
    <input type="number" name="precio" step="0.01" min="0.01" required>
    <label>Stock:</label>
    <input type="number" name="stock" min="0" required>
    <label>Proveedor:</label>
    <select name="proveedor_id">
        <option value="">Sin proveedor</option>
        <?php while ($prov = mysqli_fetch_assoc($proveedores)) { ?>
            <option value="<?php echo $prov['id']; ?>"><?php echo $prov['nombre']; ?></option>
        <?php } ?>
    </select>
    <button type="submit">Agregar</button>
</form>
<?php include '../includes/footer.php'; ?>