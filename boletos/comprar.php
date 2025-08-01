<?php
session_start();
require_once '../config/conexion.php';
require_once '../includes/header.php';

$peliculas_query = "SELECT id, titulo, sala, horario FROM peliculas WHERE horario >= NOW()";
$peliculas_result = mysqli_query($conexion, $peliculas_query);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comprar Boletos</title>
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

        /* Estilos para formularios y modal */
        .text-4xl { font-size: 2.25rem; }
        .font-bold { font-weight: 700; }
        .mb-2 { margin-bottom: 0.5rem; }
        .mb-4 { margin-bottom: 1rem; }
        .mt-4 { margin-top: 1rem; }
        .mt-6 { margin-top: 1.5rem; }
        .w-full { width: 100%; }
        .p-3 { padding: 0.75rem; }
        .border { border: 1px solid #d1d5db; }
        .rounded-md { border-radius: 6px; }
        .rounded-lg { border-radius: 0.5rem; }
        .text-center { text-align: center; }
        .text-gray-700 { color: #4b5563; }
        .text-red-500 { color: #ef4444; }
        .bg-gradient-to-r {
            background: linear-gradient(to right, #e0f2fe, #e0e7ff);
        }
        .grid { display: grid; }
        .gap-2 { gap: 0.5rem; }
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
            <h2 class="text-4xl font-bold mb-6 text-center text-gray-800 animate-pulse">Comprar Boletos</h2>
            <form id="compra-form" class="w-full">
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">Película:</label>
                    <select id="pelicula_id" name="pelicula_id" class="w-full p-3 border rounded-md" required>
                        <option value="">Selecciona una película</option>
                        <?php while ($pelicula = mysqli_fetch_assoc($peliculas_result)): ?>
                            <option value="<?php echo $pelicula['id']; ?>" data-sala="<?php echo $pelicula['sala']; ?>">
                                <?php echo htmlspecialchars($pelicula['titulo']) . ' - ' . $pelicula['horario']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">Cantidad:</label>
                    <input type="number" id="cantidad" name="cantidad" min="1" class="w-full p-3 border rounded-md" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">Asientos:</label>
                    <div id="asientos" class="grid gap-2" style="grid-template-columns: repeat(5, 1fr);"></div>
                </div>
                <button type="submit" class="btn btn-primary w-full">Confirmar Compra</button>
            </form>
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

        const peliculaSelect = document.getElementById('pelicula_id');
        const asientosDiv = document.getElementById('asientos');
        const cantidadInput = document.getElementById('cantidad');
        const compraForm = document.getElementById('compra-form');

        peliculaSelect.addEventListener('change', function() {
            const salaId = this.options[this.selectedIndex].dataset.sala;
            asientosDiv.innerHTML = '<p class="text-gray-700">Cargando asientos...</p>';

            const formData = new FormData();
            formData.append('sala_id', salaId);

            fetch('/cine/api/get_asientos.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) throw new Error('Error en la red: ' + response.status);
                return response.json();
            })
            .then(data => {
                asientosDiv.innerHTML = '';
                if (data.error) {
                    asientosDiv.innerHTML = `<p class="text-red-500">${data.error}</p>`;
                    return;
                }
                if (!Array.isArray(data)) {
                    asientosDiv.innerHTML = `<p class="text-red-500">Error: Respuesta inválida del servidor</p>`;
                    return;
                }
                data.forEach(asiento => {
                    const button = document.createElement('button');
                    button.type = 'button';
                    button.className = `btn btn-sm ${asiento.estado === 'disponible' ? 'btn-success' : 'btn-danger disabled'}`;
                    button.innerText = asiento.numero;
                    button.dataset.asientoId = asiento.id;
                    if (asiento.estado === 'disponible') {
                        button.addEventListener('click', () => {
                            if (button.classList.contains('btn-primary')) {
                                button.classList.remove('btn-primary');
                                button.classList.add('btn-success');
                            } else if (document.querySelectorAll('#asientos .btn-primary').length < cantidadInput.value) {
                                button.classList.remove('btn-success');
                                button.classList.add('btn-primary');
                            }
                        });
                    }
                    asientosDiv.appendChild(button);
                });
            })
            .catch(error => {
                asientosDiv.innerHTML = `<p class="text-red-500">Error al cargar asientos: ${error.message}</p>`;
            });
        });

        compraForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const selectedAsientos = document.querySelectorAll('#asientos .btn-primary');
            if (selectedAsientos.length !== parseInt(cantidadInput.value)) {
                showToast('Selecciona la cantidad correcta de asientos', true);
                return;
            }

            const formData = new FormData();
            formData.append('pelicula_id', peliculaSelect.value);
            formData.append('cantidad', cantidadInput.value);
            <?php if (isset($_SESSION['usuario_id'])): ?>
                formData.append('usuario_id', '<?php echo $_SESSION['usuario_id']; ?>');
            <?php else: ?>
                formData.append('usuario_id', '');
            <?php endif; ?>
            selectedAsientos.forEach((btn, index) => {
                formData.append(`asientos[${index}]`, btn.dataset.asientoId);
            });

            fetch('/cine/api/compra.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
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

        document.getElementById('close-modal').addEventListener('click', () => {
            document.getElementById('modal').classList.remove('show');
            window.location.reload();
        });

        document.querySelector('.modal .btn-close').addEventListener('click', () => {
            document.getElementById('modal').classList.remove('show');
            window.location.reload();
        });
    </script>

    <?php require_once '../includes/footer.php'; ?>
</body>
</html>