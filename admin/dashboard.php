<?php
require_once '../config/conexion.php';

// Verificar si el usuario es administrador
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'admin') {
    header("Location: /cine/index.php#login");
    exit;
}

// Obtener estadísticas
$query_peliculas = "SELECT COUNT(*) as total FROM peliculas";
$result_peliculas = mysqli_query($conexion, $query_peliculas);
$total_peliculas = $result_peliculas ? mysqli_fetch_assoc($result_peliculas)['total'] : 0;

$query_golosinas = "SELECT COUNT(*) as total FROM golosinas";
$result_golosinas = mysqli_query($conexion, $query_golosinas);
$total_golosinas = $result_golosinas ? mysqli_fetch_assoc($result_golosinas)['total'] : 0;

$query_compras = "SELECT COUNT(*) as total FROM compras_golosinas";
$result_compras = mysqli_query($conexion, $query_compras);
$total_compras = $result_compras ? mysqli_fetch_assoc($result_compras)['total'] : 0;

require_once '../includes/header.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración</title>
    <style>
        /* Animación fade-in */
        .animate-fade-in {
            animation: fadeIn 1s ease-in-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
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

        /* Estilos para formularios y estadísticas */
        .text-4xl { font-size: 2.25rem; }
        .text-xl { font-size: 1.25rem; }
        .font-bold { font-weight: 700; }
        .mb-4 { margin-bottom: 1rem; }
        .mt-4 { margin-top: 1rem; }
        .mt-6 { margin-top: 1.5rem; }
        .p-4 { padding: 1rem; }
        .text-center { text-align: center; }
        .text-gray-700 { color: #4b5563; }
        .text-red-500 { color: #ef4444; }
        .bg-gradient-to-r {
            background: linear-gradient(to right, #e0f2fe, #e0e7ff);
        }
        .card {
            background-color: #fff;
            border-radius: 6px;
            padding: 1.5rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .toast {
            position: fixed;
            bottom: 20px;
            right: 20px;
            padding: 1rem;
            border-radius: 6px;
            color: white;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .toast.success { background: #10b981; }
        .toast.error { background: #ef4444; }
        .toast.show { opacity: 1; }
    </style>
</head>
<body>
    <div class="max-w-4xl mx-auto py-8 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg">
        <section class="animate-fade-in">
            <h2 class="text-4xl font-bold mb-6 text-center text-gray-800 animate-pulse">Panel de Administración</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="card">
                    <h3 class="text-xl font-bold mb-4">Películas</h3>
                    <p class="text-gray-700">Total: <?php echo $total_peliculas; ?></p>
                    <a href="/cine/peliculas/administrar.php" class="btn btn-primary mt-4">Gestionar Películas</a>
                </div>
                <div class="card">
                    <h3 class="text-xl font-bold mb-4">Golosinas</h3>
                    <p class="text-gray-700">Total: <?php echo $total_golosinas; ?></p>
                    <a href="/cine/golosinas/administrar.php" class="btn btn-primary mt-4">Gestionar Golosinas</a>
                </div>
                <div class="card">
                    <h3 class="text-xl font-bold mb-4">Compras de Golosinas</h3>
                    <p class="text-gray-700">Total: <?php echo $total_compras; ?></p>
                </div>
            </div>
        </section>
    </div>

    <script>
        function showToast(message, isError = false) {
            const toast = document.createElement('div');
            toast.className = `toast ${isError ? 'error' : 'success'}`;
            toast.textContent = message;
            document.body.appendChild(toast);
            setTimeout(() => toast.classList.add('show'), 100);
            setTimeout(() => toast.remove(), 3000);
        }
    </script>

    <?php
    mysqli_close($conexion);
    require_once '../includes/footer.php';
    ?>
</body>
</html>