<?php
include '../includes/header.php';
include '../config/conexion.php';

// Procesar el formulario cuando se envía
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Insertar usuario en la base de datos
    $query = "INSERT INTO usuarios (nombre, email, password) VALUES ('$nombre', '$email', '$password')";
    if (mysqli_query($conexion, $query)) {
        echo "<p>Usuario registrado. <a href='login.php'>Inicia sesión</a>.</p>";
    } else {
        echo "<p>Error: " . mysqli_error($conexion) . "</p>";
    }
}
?>

<h2>Registrarse</h2>
<form method="POST" action="" onsubmit="return validarRegistro()">
    <label>Nombre:</label>
    <input type="text" id="nombre" name="nombre" required>
    <label>Email:</label>
    <input type="email" id="email" name="email" required>
    <label>Contraseña:</label>
    <input type="password" id="password" name="password" required>
    <button type="submit">Registrarse</button>
</form>
<?php include '../includes/footer.php'; ?>