<?php
require_once '../config/conexion.php';

// Verificar si el usuario es administrador
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'admin') {
    header("Location: /cine/index.php#login");
    exit;
}

// Obtener golosinas
$query = "SELECT g.id, g.nombre, g.precio, g.stock, p.nombre AS proveedor, p.id AS proveedor_id 
          FROM golosinas g 
          JOIN proveedores p ON g.proveedor_id = p.id 
          ORDER BY g.nombre";
$golosinas = mysqli_query($conexion, $query);
if (!$golosinas) {
    $error = 'Error al cargar golosinas: ' . mysqli_error($conexion);
}

// Obtener proveedores
$query = "SELECT id, nombre FROM proveedores ORDER BY nombre";
$proveedores = mysqli_query($conexion, $query);
if (!$proveedores) {
    $error = 'Error al cargar proveedores: ' . mysqli_error($conexion);
}

// Incluir el encabezado
require_once '../includes/header.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrar Golosinas</title>
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
        .btn-secondary {
            background-color: #6b7280;
            color: white;
        }
        .btn-secondary:hover {
            background-color: #4b5563;
        }
        .btn-danger {
            background-color: #ef4444;
            color: white;
        }
        .btn-danger:hover {
            background-color: #dc2626;
        }

        /* Estilos para formularios y tabla */
        .text-xl { font-size: 1.25rem; }
        .text-4xl { font-size: 2.25rem; }
        .font-bold { font-weight: 700; }
        .mb-2 { margin-bottom: 0.5rem; }
        .mb-4 { margin-bottom: 1rem; }
        .mt-4 { margin-top: 1rem; }
        .mt-6 { margin-top: 1.5rem; }
        .mt-8 { margin-top: 2rem; }
        .w-full { width: 100%; }
        .p-3 { padding: 0.75rem; }
        .border { border: 1px solid #d1d5db; }
        .rounded-md { border-radius: 6px; }
        .rounded-lg { border-radius: 0.5rem; }
        .text-center { text-align: center; }
        .text-gray-700 { color: #4b5563; }
        .text-red-500 { color: #ef4444; }
        .bg-gray-100 { background-color: #f3f4f6; }
        .border-collapse { border-collapse: collapse; }
        .bg-gradient-to-r {
            background: linear-gradient(to right, #e0f2fe, #e0e7ff);
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
            <h2 class="text-4xl font-bold mb-6 text-center text-gray-800 animate-pulse">Administrar Golosinas</h2>
            <?php if (isset($error)) { ?>
                <p class="text-red-500 mb-4 text-center"><?php echo $error; ?></p>
            <?php } ?>
            <!-- Formulario para agregar/editar golosina -->
            <form id="golosina-form" method="POST" action="">
                <input type="hidden" name="id" id="id">
                <label class="block text-gray-700 mb-2">Nombre:</label>
                <input type="text" name="nombre" id="nombre" class="w-full p-3 border rounded-md" required>
                <label class="block text-gray-700 mb-2 mt-4">Precio:</label>
                <input type="number" name="precio" id="precio" class="w-full p-3 border rounded-md" step="0.01" min="0" required>
                <label class="block text-gray-700 mb-2 mt-4">Stock:</label>
                <input type="number" name="stock" id="stock" class="w-full p-3 border rounded-md" min="0" required>
                <label class="block text-gray-700 mb-2 mt-4">Proveedor:</label>
                <select name="proveedor_id" id="proveedor_id" class="w-full p-3 border rounded-md" required>
                    <option value="">Selecciona un proveedor</option>
                    <?php while ($proveedor = mysqli_fetch_assoc($proveedores)) { ?>
                        <option value="<?php echo $proveedor['id']; ?>"><?php echo htmlspecialchars($proveedor['nombre']); ?></option>
                    <?php } ?>
                </select>
                <button type="submit" id="submit-btn" class="btn btn-primary mt-6">Agregar Golosina</button>
            </form>
            <!-- Lista de golosinas -->
            <h3 class="text-xl font-bold mb-4 mt-8">Golosinas Existentes</h3>
            <table class="w-full border-collapse border border-gray-300">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="border p-3">Nombre</th>
                        <th class="border p-3">Precio</th>
                        <th class="border p-3">Stock</th>
                        <th class="border p-3">Proveedor</th>
                        <th class="border p-3">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($golosinas) == 0) { ?>
                        <tr><td colspan="5" class="border p-3 text-center">No hay golosinas registradas.</td></tr>
                    <?php } else { ?>
                        <?php while ($golosina = mysqli_fetch_assoc($golosinas)) { ?>
                            <tr>
                                <td class="border p-3"><?php echo htmlspecialchars($golosina['nombre']); ?></td>
                                <td class="border p-3">$<?php echo number_format($golosina['precio'], 2); ?></td>
                                <td class="border p-3"><?php echo $golosina['stock']; ?></td>
                                <td class="border p-3"><?php echo htmlspecialchars($golosina['proveedor']); ?></td>
                                <td class="border p-3">
                                    <button class="btn btn-secondary edit-btn" 
                                            data-id="<?php echo $golosina['id']; ?>" 
                                            data-nombre="<?php echo htmlspecialchars($golosina['nombre']); ?>" 
                                            data-precio="<?php echo $golosina['precio']; ?>" 
                                            data-stock="<?php echo $golosina['stock']; ?>" 
                                            data-proveedor-id="<?php echo $golosina['proveedor_id']; ?>">Editar</button>
                                    <button class="btn btn-danger delete-btn" data-id="<?php echo $golosina['id']; ?>">Eliminar</button>
                                </td>
                            </tr>
                        <?php } ?>
                    <?php } ?>
                </tbody>
            </table>
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

        document.getElementById('golosina-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const data = {
                id: formData.get('id'),
                nombre: formData.get('nombre'),
                precio: parseFloat(formData.get('precio')),
                stock: parseInt(formData.get('stock')),
                proveedor_id: parseInt(formData.get('proveedor_id'))
            };
            const method = data.id ? 'PUT' : 'POST';

            fetch('/cine/api/golosina.php', {
                method: method,
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            })
            .then(response => {
                if (!response.ok) throw new Error('Error en la red: ' + response.status);
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    showToast(data.success, false);
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    showToast('Error: ' + data.error, true);
                }
            })
            .catch(error => showToast('Error: ' + error.message, true));
        });

        document.querySelectorAll('.edit-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                document.getElementById('id').value = btn.dataset.id;
                document.getElementById('nombre').value = btn.dataset.nombre;
                document.getElementById('precio').value = btn.dataset.precio;
                document.getElementById('stock').value = btn.dataset.stock;
                document.getElementById('proveedor_id').value = btn.dataset.proveedorId;
                const submitBtn = document.getElementById('submit-btn');
                submitBtn.textContent = 'Editar Golosina';
                submitBtn.classList.add('btn-primary');
            });
        });

        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                if (confirm('¿Estás seguro de eliminar esta golosina?')) {
                    fetch('/cine/api/golosina.php', {
                        method: 'DELETE',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ id: btn.dataset.id })
                    })
                    .then(response => {
                        if (!response.ok) throw new Error('Error en la red: ' + response.status);
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            showToast('Golosina eliminada.', false);
                            setTimeout(() => window.location.reload(), 1000);
                        } else {
                            showToast('Error: ' + data.error, true);
                        }
                    })
                    .catch(error => showToast('Error: ' + error.message, true));
                }
            });
        });
    </script>

    <?php
    mysqli_close($conexion);
    require_once '../includes/footer.php';
    ?>
</body>
</html>