<?php
include '../includes/header.php';
include '../config/conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $golosina_id = (int)$_POST['golosina_id'];
    $cantidad = (int)$_POST['cantidad'];
    $usuario_id = isset($_SESSION['usuario_id']) ? $_SESSION['usuario_id'] : null;

    $query_golosina = "SELECT precio, stock, nombre FROM golosinas WHERE id = $golosina_id";
    $resultado_golosina = mysqli_query($conexion, $query_golosina);
    $golosina = mysqli_fetch_assoc($resultado_golosina);

    if (!$golosina) {
        echo "<p>Error: Golosina no encontrada.</p>";
    } elseif ($golosina['stock'] < $cantidad) {
        echo "<p>Error: Stock insuficiente para " . htmlspecialchars($golosina['nombre']) . ".</p>";
    } else {
        $descuento = 1.0;
        if ($usuario_id) {
            $query_usuario = "SELECT membresia FROM usuarios WHERE id = $usuario_id";
            $resultado_usuario = mysqli_query($conexion, $query_usuario);
            $membresia = mysqli_fetch_assoc($resultado_usuario)['membresia'];
            if ($membresia == 'basica') $descuento = 0.9;
            elseif ($membresia == 'premium') $descuento = 0.8;
        }
        $query_promo = "SELECT id, descuento FROM promociones WHERE CURDATE() BETWEEN fecha_inicio AND fecha_fin";
        $resultado_promo = mysqli_query($conexion, $query_promo);
        $promocion = mysqli_fetch_assoc($resultado_promo);
        $descuento_promo = $promocion ? (1 - $promocion['descuento']) : 1.0;
        $precio_final = $golosina['precio'] * $cantidad * $descuento * $descuento_promo;

        $query = "INSERT INTO compras_golosinas (golosina_id, usuario_id, cantidad, precio_final) VALUES ($golosina_id, " . ($usuario_id ? $usuario_id : 'NULL') . ", $cantidad, $precio_final)";
        if (mysqli_query($conexion, $query)) {
            $compra_id = mysqli_insert_id($conexion);
            $query_factura = "INSERT INTO facturas (usuario_id, compra_golosina_id, promocion_id, total) VALUES (" . ($usuario_id ? $usuario_id : 'NULL') . ", $compra_id, " . ($promocion ? $promocion['id'] : 'NULL') . ", $precio_final)";
            mysqli_query($conexion, $query_factura);
            mysqli_query($conexion, "UPDATE golosinas SET stock = " . ($golosina['stock'] - $cantidad) . " WHERE id = $golosina_id");
            echo "<p>Compra de " . htmlspecialchars($golosina['nombre']) . " realizada correctamente. <a href='historial.php'>Ver historial</a></p>";
        } else {
            echo "<p>Error al realizar compra: " . mysqli_error($conexion) . "</p>";
        }
    }
}

$golosinas = mysqli_query($conexion, "SELECT id, nombre, precio, stock FROM golosinas");
?>

<h2>Comprar Golosinas</h2>
<form method="POST" action="" class="max-w-md mx-auto">
    <label class="block text-gray-700 mb-2">Golosina:</label>
    <select name="golosina_id" class="w-full p-3 border rounded-md" required>
        <option value="">Selecciona una golosina</option>
        <?php while ($gol = mysqli_fetch_assoc($golosinas)) { ?>
            <option value="<?php echo $gol['id']; ?>"><?php echo htmlspecialchars($gol['nombre']) . " ($" . number_format($gol['precio'], 2) . ", Stock: " . $gol['stock'] . ")"; ?></option>
        <?php } ?>
    </select>
    <label class="block text-gray-700 mb-2 mt-4">Cantidad:</label>
    <input type="number" name="cantidad" min="1" class="w-full p-3 border rounded-md" required>
    <?php if (isset($_SESSION['membresia']) && $_SESSION['membresia'] != 'ninguna') { ?>
        <p class="text-gray-600 mt-2">Membresía detectada: <?php echo $_SESSION['membresia']; ?> (Descuento aplicado)</p>
    <?php } else { ?>
        <p class="text-gray-600 mt-2">Sin membresía. Precio normal.</p>
    <?php } ?>
    <button type="submit" class="w-full bg-blue-500 text-white p-3 rounded-md mt-4 hover:bg-blue-600">Comprar</button>
</form>
<?php include '../includes/footer.php'; ?>