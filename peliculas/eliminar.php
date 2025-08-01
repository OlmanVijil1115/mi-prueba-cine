<?php
// Iniciar la sesión para verificar permisos
session_start();
// Verificar si el usuario es administrador
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: /cine/index.php");
    exit;
}

// Incluir la conexión a la base de datos
include '../config/conexion.php';

// Obtener el ID de la película desde la URL
$pelicula_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Validar que el ID sea válido
if ($pelicula_id <= 0) {
    echo "<p class='text-red-500'>Error: ID de película no válido.</p>";
    include '../includes/footer.php';
    exit;
}

// Iniciar una transacción para asegurar consistencia
mysqli_begin_transaction($conexion);

try {
    // Obtener la sala asociada a la película
    $query_sala = "SELECT sala FROM peliculas WHERE id = $pelicula_id";
    $resultado_sala = mysqli_query($conexion, $query_sala);
    if (!$resultado_sala || mysqli_num_rows($resultado_sala) == 0) {
        throw new Exception("Película no encontrada.");
    }
    $sala = mysqli_fetch_assoc($resultado_sala)['sala'];

    // Eliminar los asientos asociados a la sala
    $query_asientos = "DELETE FROM asientos WHERE sala = $sala";
    if (!mysqli_query($conexion, $query_asientos)) {
        throw new Exception("Error al eliminar asientos: " . mysqli_error($conexion));
    }

    // Eliminar los boletos asociados a la película
    $query_boletos = "DELETE FROM boletos WHERE pelicula_id = $pelicula_id";
    if (!mysqli_query($conexion, $query_boletos)) {
        throw new Exception("Error al eliminar boletos: " . mysqli_error($conexion));
    }

    // Eliminar las facturas asociadas a los boletos de la película
    $query_facturas = "DELETE FROM facturas WHERE boleto_id IN (SELECT id FROM boletos WHERE pelicula_id = $pelicula_id)";
    if (!mysqli_query($conexion, $query_facturas)) {
        throw new Exception("Error al eliminar facturas: " . mysqli_error($conexion));
    }

    // Eliminar la película
    $query_pelicula = "DELETE FROM peliculas WHERE id = $pelicula_id";
    if (!mysqli_query($conexion, $query_pelicula)) {
        throw new Exception("Error al eliminar película: " . mysqli_error($conexion));
    }

    // Confirmar la transacción
    mysqli_commit($conexion);
    echo "<p class='text-green-500'>Película, asientos y registros asociados eliminados correctamente.</p>";
    echo "<a href='/cine/peliculas/administrar.php' class='text-blue-500'>Volver a administrar películas</a>";
} catch (Exception $e) {
    // Revertir la transacción si hay un error
    mysqli_rollback($conexion);
    echo "<p class='text-red-500'>Error: " . $e->getMessage() . "</p>";
}

// Incluir el pie de página
include '../includes/footer.php';
?>