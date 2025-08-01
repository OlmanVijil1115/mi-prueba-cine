<?php
include '../includes/header.php';
include '../config/conexion.php';

if (!isset($_GET['id'])) {
    echo "<p>Error: ID de golosina no proporcionado.</p>";
    include '../includes/footer.php';
    exit;
}

$id = (int)$_GET['id'];
$query = "SELECT * FROM golosinas WHERE id = $id";
$resultado = mysqli_query($conexion, $query);
$golosina = mysqli_fetch_assoc($resultado);

if (!$golosina) {
    echo "<p>Error: Golosina no encontrada.</p>";
    include '../includes/footer.php';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = trim($_POST['nombre']);
    $precio = (float)$_POST['precio'];
    $stock = (int)$_POST['stock'];
    $proveedor_id = !empty($_POST['proveedor_id']) ? (int)$_POST['proveedor_id'] : null;

    if (empty($nombre) || $precio <= 0 || $stock < 0) {
        echo "<p>Error: Todos los campos son obligatorios y deben ser válidos.</p>";
    } else {
        $query = "UPDATE golosinas SET nombre = '$nombre', precio = $precio, stock = $stock, proveedor_id = " . ($proveedor_id ? $proveedor_id : 'NULL') . " WHERE id = $id";
        if (mysqli_query($conexion, $query)) {
            echo "<p>Golosina actualizada correctamente. <a href='index.php'>Volver</a></p>";
        } else {
            echo "<p>Error al actualizar golosina: " . mysqli_error($conexion) . "</p>";
        }
    }
}

$proveedores = mysqli_query($conexion, "SELECT id, nombre FROM proveedores");
?>
<h2>Editar Golosina</h2>
<form method="POST" action="">
    <label>Nombre:</label>
    <input type="text" name="nombre" value="<?php echo htmlspecialchars($golosina['nombre']); ?>" required>
    <label>Precio:</label>
    <input type="number" name="precio" value="<?php echo $golosina['precio']; ?>" step="0.01" min="0.01" required>
    <label>Stock:</label>
    <input type="number" name="stock" value="<?php echo $golosina['stock']; ?>" min="0" required>
    <label>Proveedor:</label>
    <select name="proveedor_id">
        <option value="">Sin proveedor</option>
        <?php while ($prov = mysqli_fetch_assoc($proveedores)) { ?>
            <option value="<?php echo $prov['id']; ?>" <?php echo ($prov['id'] == $golosina['proveedor_id']) ? 'selected' : ''; ?>><?php echo $prov['nombre']; ?></option>
        <?php } ?>
    </select>
    <button type="submit">Actualizar</button>
</form>
<?php include '../includes/footer.php'; ?>