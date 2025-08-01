<?php
require_once '../config/conexion.php';
require_once '../includes/header.php';

$query = "SELECT id, nombre, precio, stock FROM golosinas WHERE stock > 0 ORDER BY nombre";
$golosinas = mysqli_query($conexion, $query);
if (!$golosinas) {
    $error = 'Error al cargar golosinas: ' . mysqli_error($conexion);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comprar Golosinas</title>
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
        .btn-success {
            background-color: #10b981;
            color: white;
        }
        .btn-success:hover {
            background-color: #059669;
        }
        .btn-danger {
            background-color: #ef4444;
            color: white;
        }
        .btn-danger:hover {
            background-color: #dc2626;
        }
        .btn-secondary {
            background-color: #6b7280;
            color: white;
        }
        .btn-secondary:hover {
            background-color: #4b5563;
        }
        .btn.disabled {
            background-color: #d1d5db;
            cursor: not-allowed;
        }

        /* Estilos para formularios, tabla y modal */
        .text-4xl { font-size: 2.25rem; }
        .text-xl { font-size: 1.25rem; }
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
        .modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.6);
            display: none;
            justify-content: center;
            align-items: center;
        }
        .modal.show {
            display: flex;
        }
        .modal-content {
            background-color: #fff;
            border-radius: 10px;
            padding: 20px;
            max-width: 600px;
            width: 90%;
        }
        .modal-header, .modal-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
        }
        .modal-header {
            background-color: #4b5563;
            color: white;
            border-radius: 8px 8px 0 0;
        }
        .btn-close {
            background: none;
            border: none;
            font-size: 1.2em;
            color: white;
            cursor: pointer;
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
            <h2 class="text-4xl font-bold mb-6 text-center text-gray-800 animate-pulse">Comprar Golosinas</h2>
            <?php if (isset($error)) { ?>
                <p class="text-red-500 mb-4 text-center"><?php echo $error; ?></p>
            <?php } ?>
            <h3 class="text-xl font-bold mb-4 mt-8">Golosinas Disponibles</h3>
            <table class="w-full border-collapse border border-gray-300">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="border p-3">Nombre</th>
                        <th class="border p-3">Precio</th>
                        <th class="border p-3">Stock</th>
                        <th class="border p-3">Cantidad</th>
                        <th class="border p-3">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($golosinas) == 0) { ?>
                        <tr><td colspan="5" class="border p-3 text-center">No hay golosinas disponibles.</td></tr>
                    <?php } else { ?>
                        <?php while ($golosina = mysqli_fetch_assoc($golosinas)) { ?>
                            <tr>
                                <td class="border p-3"><?php echo htmlspecialchars($golosina['nombre']); ?></td>
                                <td class="border p-3">$<?php echo number_format($golosina['precio'], 2); ?></td>
                                <td class="border p-3"><?php echo $golosina['stock']; ?></td>
                                <td class="border p-3">
                                    <input type="number" class="w-full p-3 border rounded-md cantidad-input" data-id="<?php echo $golosina['id']; ?>" min="1" max="<?php echo $golosina['stock']; ?>" value="1">
                                </td>
                                <td class="border p-3">
                                    <button class="btn btn-primary comprar-btn" data-id="<?php echo $golosina['id']; ?>" data-nombre="<?php echo htmlspecialchars($golosina['nombre']); ?>" data-precio="<?php echo $golosina['precio']; ?>">Comprar</button>
                                </td>
                            </tr>
                        <?php } ?>
                    <?php } ?>
                </tbody>
            </table>
        </section>
    </div>

    <div id="modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="text-xl font-bold">Factura</h5>
                <button type="button" class="btn-close">×</button>
            </div>
            <div class="modal-body" id="factura-content"></div>
            <div class="modal-footer">
                <button id="close-modal" class="btn btn-secondary">Cerrar</button>
            </div>
        </div>
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

        document.querySelectorAll('.comprar-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const golosinaId = btn.dataset.id;
                const nombre = btn.dataset.nombre;
                const precio = parseFloat(btn.dataset.precio);
                const cantidadInput = document.querySelector(`.cantidad-input[data-id="${golosinaId}"]`);
                const cantidad = parseInt(cantidadInput.value);

                if (cantidad <= 0 || isNaN(cantidad)) {
                    showToast('Selecciona una cantidad válida', true);
                    return;
                }

                if (!confirm(`¿Estás seguro de comprar ${cantidad} ${nombre} por $${(precio * cantidad).toFixed(2)}?`)) {
                    return;
                }

                const formData = new FormData();
                formData.append('golosina_id', golosinaId);
                formData.append('cantidad', cantidad);
                formData.append('precio', precio);
                <?php if (isset($_SESSION['usuario_id'])): ?>
                    formData.append('usuario_id', '<?php echo $_SESSION['usuario_id']; ?>');
                <?php else: ?>
                    formData.append('usuario_id', '');
                <?php endif; ?>

                fetch('/cine/api/compra_golosina.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    if (!response.ok) throw new Error('Error en la red: ' + response.status);
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        document.getElementById('factura-content').innerHTML = data.factura_html;
                        document.getElementById('modal').classList.add('show');
                    } else {
                        showToast('Error: ' + data.error, true);
                    }
                })
                .catch(error => showToast('Error: ' + error.message, true));
            });
        });

        document.getElementById('close-modal').addEventListener('click', () => {
            document.getElementById('modal').classList.remove('show');
            window.location.reload();
        });

        document.querySelector('.modal .btn-close').addEventListener('click', () => {
            document.getElementById('modal').classList.remove('show');
            window.location.reload();
        });
    </script>

    <?php
    mysqli_close($conexion);
    require_once '../includes/footer.php';
    ?>
</body>
</html>