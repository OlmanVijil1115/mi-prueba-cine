<?php
include '../includes/header.php';
include '../config/conexion.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

// Procesar el formulario cuando se envía
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $membresia = $_POST['membresia'];
    $usuario_id = $_SESSION['usuario_id'];

    // Actualizar membresía en la base de datos
    $query = "UPDATE usuarios SET membresia = '$membresia' WHERE id = $usuario_id";
    if (mysqli_query($conexion, $query)) {
        $_SESSION['membresia'] = $membresia;
        echo "<p>Membresía actualizada.</p>";
    } else {
        echo "<p>Error: " . mysqli_error($conexion) . "</p>";
    }
}

// Obtener información del usuario
$usuario_id = $_SESSION['usuario_id'];
$usuario = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT membresia FROM usuarios WHERE id = $usuario_id"));
?>

<h2>Gestionar Membresía</h2>
<form method="POST" action="">
    <label>Tipo de Membresía:</label>
    <select name="membresia">
        <option value="ninguna" <?php if ($usuario['membresia'] == 'ninguna') echo 'selected'; ?>>Ninguna</option>
        <option value="basica" <?php if ($usuario['membresia'] == 'basica') echo 'selected'; ?>>Básica (10% descuento)</option>
        <option value="premium" <?php if ($usuario['membresia'] == 'premium') echo 'selected'; ?>>Premium (20% descuento)</option>
    </select>
    <button type="submit">Actualizar</button>
</form>
<?php include '../includes/footer.php'; ?>