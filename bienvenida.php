<?php
// Iniciar la sesión y conectar a la base de datos
require_once 'config/conexion.php';

// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: /cine/index.php#login");
    exit;
}

// Obtener el nombre y membresía del usuario
$usuario_id = $_SESSION['usuario_id'];
$query = "SELECT nombre, membresia, rol FROM usuarios WHERE id = '$usuario_id'";
$resultado = mysqli_query($conexion, $query);
if ($resultado && mysqli_num_rows($resultado) > 0) {
    $usuario = mysqli_fetch_assoc($resultado);
    $nombre_usuario = htmlspecialchars($usuario['nombre']);
    $membresia = $usuario['membresia'];
    $rol = $usuario['rol'];
    $mensaje_membresia = $membresia === 'premium' ? 'Disfruta de descuentos exclusivos como miembro Premium.' : 'Explora la opción Premium para beneficios adicionales.';
} else {
    $nombre_usuario = 'Usuario';
    $mensaje_membresia = 'Explora la opción Premium para beneficios adicionales.';
    $rol = 'cliente';
}

// Incluir el encabezado
require_once 'includes/header.php';
?>

<!-- Contenido principal -->
<div class="max-w-2xl mx-auto py-8 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg">
    <section class="animate-fade-in">
        <h2 class="text-3xl font-bold mb-6 text-center text-gray-800 animate-pulse">¡Bienvenido, <?php echo $nombre_usuario; ?>!</h2>
        <p class="text-gray-600 mb-6 text-center"><?php echo $mensaje_membresia; ?></p>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <a href="/cine/boletos/comprar.php" class="btn btn-primary animate-slide-in">Comprar Boletos</a>
            <a href="/cine/golosinas/comprar.php" class="btn btn-primary animate-slide-in">Comprar Golosinas</a>
            <a href="/cine/compras/historial.php" class="btn btn-secondary animate-slide-in">Ver Historial de Compras</a>
            <?php if ($rol === 'admin') { ?>
                <a href="/cine/admin/dashboard.php" class="btn btn-primary animate-slide-in">Panel de Administración</a>
                <a href="/cine/peliculas/administrar.php" class="btn btn-secondary animate-slide-in">Administrar Películas</a>
                <a href="/cine/golosinas/administrar.php" class="btn btn-primary animate-slide-in">Administrar Golosinas</a>
            <?php } ?>
        </div>
        <div class="mt-8 text-center">
            <a href="javascript:openLogoutModal()" class="btn btn-logout animate-slide-in">Cerrar Sesión</a>
        </div>
    </section>
</div>

<!-- JavaScript para confirmación de cierre de sesión -->
<script>
function openLogoutModal() {
    if (confirm('¿Estás seguro de cerrar sesión?')) {
        window.location.href = '/cine/logout.php';
    }
}
</script>

<!-- Estilos para animaciones y diseño -->
<style>
/* Animación fade-in */
.animate-fade-in {
    animation: fadeIn 1s ease-in-out;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Animación slide-in */
.animate-slide-in {
    animation: slideIn 0.5s ease-in-out;
}
@keyframes slideIn {
    from { opacity: 0; transform: translateX(-20px); }
    to { opacity: 1; transform: translateX(0); }
}

/* Animación pulse */
.animate-pulse {
    animation: pulse 2s infinite;
}
@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

/* Animación de fondo gradiente */
.bg-gradient-to-r {
    background: linear-gradient(to right, #e0f2fe, #e0e7ff);
    animation: gradientShift 10s ease infinite;
}
@keyframes gradientShift {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}

/* Estilos para botones */
.btn {
    display: inline-block;
    padding: 0.75rem 1.5rem;
    border-radius: 6px;
    font-weight: 500;
    text-align: center;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.btn:hover {
    transform: scale(1.05);
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}
.btn-primary {
    background-color: #3b82f6;
    color: white;
}
.btn-primary:hover {
    background-color: #2563eb;
}
.btn-secondary {
    background-color: #6b7280;
    color: white;
}
.btn-secondary:hover {
    background-color: #4b5563;
}
.btn-logout {
    background-color: #ef4444;
    color: white;
}
.btn-logout:hover {
    background-color: #dc2626;
}

/* Compatibilidad con Tailwind */
.grid { display: grid; }
.grid-cols-1 { grid-template-columns: repeat(1, minmax(0, 1fr)); }
.sm\:grid-cols-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
.gap-4 { gap: 1rem; }
.text-3xl { font-size: 1.875rem; }
.text-gray-800 { color: #1f2937; }
.text-gray-600 { color: #4b5563; }
.text-center { text-align: center; }
.mb-6 { margin-bottom: 1.5rem; }
.mt-8 { margin-top: 2rem; }
.rounded-lg { border-radius: 0.5rem; }
</style>

<?php
// Incluir el pie de página
require_once 'includes/footer.php';

// Cerrar la conexión
mysqli_close($conexion);
?>