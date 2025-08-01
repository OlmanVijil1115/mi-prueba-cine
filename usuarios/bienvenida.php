<?php
include '../includes/header.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}
?>

<h2>¡Bienvenido, <?php echo htmlspecialchars($_SESSION['nombre']); ?>!</h2>
<p>Has iniciado sesión correctamente.</p>
<p><a href="../dashboard/index.php">Ir al Dashboard</a></p>
<p><a href="logout.php">Cerrar Sesión</a></p>

<?php include '../includes/footer.php'; ?>