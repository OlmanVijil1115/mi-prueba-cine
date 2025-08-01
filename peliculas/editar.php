<?php
include '../includes/header.php';
include '../config/conexion.php';

if (!isset($_GET['id'])) {
    echo "<p>Error: ID de película no proporcionado.</p>";
    include '../includes/footer.php';
    exit;
}

$id = (int)$_GET['id'];
$query = "SELECT * FROM peliculas WHERE id = $id";
$resultado = mysqli_query($conexion, $query);
$pelicula = mysqli_fetch_assoc($resultado);

if (!$pelicula) {
    echo "<p>Error: Película no encontrada.</p>";
    include '../includes/footer.php';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $titulo = trim($_POST['titulo']);
    $sala = (int)$_POST['sala'];
    $horario = $_POST['horario'];
    $precio = (float)$_POST['precio'];
    $proveedor_id = !empty($_POST['proveedor_id']) ? (int)$_POST['proveedor_id'] : null;

    if (empty($titulo) || $sala <= 0 || empty($horario) || $precio <= 0) {
        echo "<p>Error: Todos los campos son obligatorios y deben ser válidos.</p>";
    } else {
        // Verificar si la sala cambió
        $sala_anterior = $pelicula['sala'];
        $query = "UPDATE peliculas SET titulo = '$titulo', sala = $sala, horario = '$horario', precio = $precio, proveedor_id = " . ($proveedor_id ? $proveedor_id : 'NULL') . " WHERE id = $id";
        if (mysqli_query($conexion, $query)) {
            if ($sala != $sala_anterior) {
                // Verificar si la nueva sala ya tiene asientos
                $query_sala = "SELECT COUNT(*) AS total FROM asientos WHERE sala = $sala";
                $resultado_sala = mysqli_query($conexion, $query_sala);
                $total_asientos = mysqli_fetch_assoc($resultado_sala)['total'];

                if ($total_asientos == 0) {
                    // Generar 10 asientos (A1-A5, B1-B5)
                    $asientos = ['A1', 'A2', 'A3', 'A4', 'A5', 'B1', 'B2', 'B3', 'B4', 'B5'];
                    foreach ($asientos as $numero) {
                        $query_asiento = "INSERT INTO asientos (sala, numero, estado) VALUES ($sala, '$numero', 'disponible')";
                        mysqli_query($conexion, $query_asiento);
                    }
                }
            }
            echo "<p>Película actualizada correctamente. <a href='index.php'>Volver</a></p>";
        } else {
            echo "<p>Error al actualizar película: " . mysqli_error($conexion) . "</p>";
        }
    }
}

$proveedores = mysqli_query($conexion, "SELECT id, nombre FROM proveedores");
?>
<h2>Editar Película</h2>
<form method="POST" action="" class="max-w-md mx-auto">
    <label class="block text-gray-700 mb-2">Título:</label>
    <input type="text" name="titulo" value="<?php echo htmlspecialchars($pelicula['titulo']); ?>" class="w-full p-3 border rounded-md" required>
    <label class="block text-gray-700 mb-2 mt-4">Sala:</label>
    <input type="number" name="sala" value="<?php echo $pelicula['sala']; ?>" min="1" class="w-full p-3 border rounded-md" required>
    <label class="block text-gray-700 mb-2 mt-4">Horario:</label>
    <input type="datetime-local" name="horario" value="<?php echo date('Y-m-d\TH:i', strtotime($pelicula['horario'])); ?>" class="w-full p-3 border rounded-md" required>
    <label class="block text-gray-700 mb-2 mt-4">Precio:</label>
    <input type="number" name="precio" value="<?php echo $pelicula['precio']; ?>" step="0.01" min="0.01" class="w-full p-3 border rounded-md" required>
    <label class="block text-gray-700 mb-2 mt-4">Proveedor:</label>
    <select name="proveedor_id" class="w-full p-3 border rounded-md">
        <option value="">Sin proveedor</option>
        <?php while ($prov = mysqli_fetch_assoc($proveedores)) { ?>
            <option value="<?php echo $prov['id']; ?>" <?php echo ($prov['id'] == $pelicula['proveedor_id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($prov['nombre']); ?></option>
        <?php } ?>
    </select>
    <button type="submit" class="w-full bg-blue-500 text-white p-3 rounded-md mt-4 hover:bg-blue-600">Actualizar</button>
</form>
<?php include '../includes/footer.php'; ?>