<?php
// Iniciar la sesión para verificar permisos
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: /cine/index.php");
    exit;
}

// Incluir la conexión a la base de datos
include '../config/conexion.php';

// Procesar el formulario cuando se envía
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtener datos del formulario
    $titulo = mysqli_real_escape_string($conexion, $_POST['titulo']);
    $sala = (int)$_POST['sala'];
    $precio = (float)$_POST['precio'];

    // Validar datos
    if (empty($titulo) || $sala <= 0 || $precio <= 0) {
        echo "<p class='text-red-500'>Error: Todos los campos son obligatorios y deben ser válidos.</p>";
    } else {
        // Iniciar una transacción para asegurar consistencia
        mysqli_begin_transaction($conexion);

        try {
            // Eliminar asientos existentes para la sala
            $query_delete_asientos = "DELETE FROM asientos WHERE sala = $sala";
            if (!mysqli_query($conexion, $query_delete_asientos)) {
                throw new Exception("Error al eliminar asientos antiguos: " . mysqli_error($conexion));
            }

            // Insertar la película
            $query_pelicula = "INSERT INTO peliculas (titulo, sala, precio) VALUES ('$titulo', $sala, $precio)";
            if (!mysqli_query($conexion, $query_pelicula)) {
                throw new Exception("Error al agregar película: " . mysqli_error($conexion));
            }

            // Generar asientos nuevos (A1-A5, B1-B5)
            $asientos = ['A1', 'A2', 'A3', 'A4', 'A5', 'B1', 'B2', 'B3', 'B4', 'B5'];
            foreach ($asientos as $numero) {
                $query_asiento = "INSERT INTO asientos (sala, numero, estado) VALUES ($sala, '$numero', 'disponible')";
                if (!mysqli_query($conexion, $query_asiento)) {
                    throw new Exception("Error al crear asiento $numero: " . mysqli_error($conexion));
                }
            }

            // Confirmar la transacción
            mysqli_commit($conexion);
            echo "<p class='text-green-500'>Película y asientos agregados correctamente.</p>";
            echo "<a href='/cine/peliculas/administrar.php' class='text-blue-500'>Volver a administrar películas</a>";
        } catch (Exception $e) {
            // Revertir la transacción si hay un error
            mysqli_rollback($conexion);
            echo "<p class='text-red-500'>Error: " . $e->getMessage() . "</p>";
        }
    }
}
?>

<!-- Formulario para agregar una película -->
<h2>Agregar Película</h2>
<div class="max-w-2xl mx-auto py-4">
    <form method="POST" action="" class="mb-8">
        <label class="block text-gray-700 mb-2">Título:</label>
        <input type="text" name="titulo" class="w-full p-3 border rounded-md" required>
        <label class="block text-gray-700 mb-2 mt-4">Sala:</label>
        <input type="number" name="sala" min="1" class="w-full p-3 border rounded-md" required>
        <label class="block text-gray-700 mb-2 mt-4">Precio:</label>
        <input type="number" name="precio" min="0.01" step="0.01" class="w-full p-3 border rounded-md" required>
        <button type="submit" class="w-full bg-blue-500 text-white p-3 rounded-md mt-4 hover:bg-blue-600">Agregar Película</button>
    </form>
</div>
<?php
// Incluir el pie de página
include '../includes/footer.php';
?>