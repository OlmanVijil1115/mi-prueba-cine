<?php
include '../includes/header.php';
include '../config/conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = trim($_POST['nombre']);
    $descripcion = trim($_POST['descripcion']);
    $descuento = (float)$_POST['descuento'];
    $fecha_inicio = $_POST['fecha_inicio'];
    $fecha_fin = $_POST['fecha_fin'];

    if (empty($nombre) || $descuento <= 0 || $descuento > 1 || empty($fecha_inicio) || empty($fecha_fin)) {
        echo "<p>Error: Todos los campos son obligatorios y el descuento debe estar entre 0 y 1.</p>";
    } else {
        $query = "INSERT INTO promociones (nombre, descripcion, descuento, fecha_inicio, fecha_fin) VALUES ('$nombre', '$descripcion', $descuento, '$fecha_inicio', '$fecha_fin')";
        if (mysqli_query($conexion, $query)) {
            echo "<p>Promoción agregada correctamente. <a href='index.php'>Volver</a></p>";
        } else {
            echo "<p>Error al agregar promoción: " . mysqli_error($conexion) . "</p>";
        }
    }
}
?>
<h2>Agregar Promoción</h2>
<form method="POST" action="">
    <label>Nombre:</label>
    <input type="text" name="nombre" required>
    <label>Descripción:</label>
    <textarea name="descripcion" class="w-full p-3 border rounded-md"></textarea>
    <label>Descuento (0 a 1):</label>
    <input type="number" name="descuento" step="0.01" min="0" max="1" required>
    <label>Fecha Inicio:</label>
    <input type="date" name="fecha_inicio" required>
    <label>Fecha Fin:</label>
    <input type="date" name="fecha_fin" required>
    <button type="submit">Agregar</button>
</form>
<?php include '../includes/footer.php'; ?>