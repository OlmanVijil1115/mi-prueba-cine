<?php
include '../includes/header.php';
include '../config/conexion.php';

if (!isset($_GET['id'])) {
    echo "<p>Error: ID de proveedor no proporcionado.</p>";
    include '../includes/footer.php';
    exit;
}

$id = (int)$_GET['id'];
$query = "SELECT * FROM proveedores WHERE id = $id";
$resultado = mysqli_query($conexion, $query);
$proveedor = mysqli_fetch_assoc($resultado);

if (!$proveedor) {
    echo "<p>Error: Proveedor no encontrado.</p>";
    include '../includes/footer.php';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = trim($_POST['nombre']);
    $contacto = trim($_POST['contacto']);

    if (empty($nombre)) {
        echo "<p>Error: El nombre es obligatorio.</p>";
    } else {
        $query = "UPDATE proveedores SET nombre = '$nombre', contacto = " . ($contacto ? "'$contacto'" : 'NULL') . " WHERE id = $id";
        if (mysqli_query($conexion, $query)) {
            echo "<p>Proveedor actualizado correctamente. <a href='index.php'>Volver</a></p>";
        } else {
            echo "<p>Error al actualizar proveedor: " . mysqli_error($conexion) . "</p>";
        }
    }
}
?>
<h2>Editar Proveedor</h2>
<form method="POST" action="">
    <label>Nombre:</label>
    <input type="text" name="nombre" value="<?php echo htmlspecialchars($proveedor['nombre']); ?>" required>
    <label>Contacto:</label>
    <input type="text" name="contacto" value="<?php echo htmlspecialchars($proveedor['contacto'] ?? ''); ?>">
    <button type="submit">Actualizar</button>
</form>
<?php include '../includes/footer.php'; ?>