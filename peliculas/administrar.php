<?php
require_once '../config/conexion.php';
require_once '../includes/header.php';

// Verificar que el usuario es admin
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header('Location: /cine/index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrar Películas y Salas</title>
    <style>
        .animate-fade-in { animation: fadeIn 1s ease-in-out; }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-pulse { animation: pulse 2s infinite; }
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        .btn {
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-weight: 500;
            text-align: center;
            transition: transform 0.2s ease;
        }
        .btn:hover:not(:disabled) { transform: scale(1.05); }
        .btn-primary { background-color: #3b82f6; color: white; }
        .btn-secondary { background-color: #6b7280; color: white; }
        .btn-danger { background-color: #ef4444; color: white; }
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }
        .modal-content {
            background: white;
            padding: 1rem;
            border-radius: 6px;
            width: 90%;
            max-width: 500px;
        }
        .modal-header { background: #4b5563; color: white; padding: 0.5rem; }
        .text-4xl { font-size: 2.25rem; }
        .font-bold { font-weight: 700; }
        .mb-4 { margin-bottom: 1rem; }
        .mt-4 { margin-top: 1rem; }
        .w-full { width: 100%; }
        .p-3 { padding: 0.75rem; }
        .border { border: 1px solid #d1d5db; }
        .rounded-md { border-radius: 6px; }
        .text-center { text-align: center; }
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
    <div class="max-w-6xl mx-auto py-8 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg">
        <h2 class="text-4xl font-bold mb-6 text-center text-gray-800 animate-pulse">Administrar Películas</h2>
        
        <!-- Formulario para agregar/editar películas -->
        <section class="animate-fade-in mb-8">
            <h3 class="text-2xl font-bold mb-4">Agregar/Editar Película</h3>
            <form id="pelicula-form" class="w-full">
                <input type="hidden" id="pelicula_id">
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">Título:</label>
                    <input type="text" id="titulo" class="w-full p-3 border rounded-md" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">Sala:</label>
                    <select id="sala" class="w-full p-3 border rounded-md" required>
                        <option value="1">Sala 1</option>
                        <option value="2">Sala 2</option>
                        <option value="3">Sala 3</option>
                        <option value="4">Sala 4</option>
                        <option value="5">Sala 5</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">Horario:</label>
                    <input type="datetime-local" id="horario" class="w-full p-3 border rounded-md" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">Precio:</label>
                    <input type="number" id="precio" step="0.01" class="w-full p-3 border rounded-md" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">Proveedor:</label>
                    <select id="proveedor_id" class="w-full p-3 border rounded-md" required>
                        <?php
                        $query = "SELECT id, nombre FROM proveedores";
                        $result = mysqli_query($conexion, $query);
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<option value='{$row['id']}'>" . htmlspecialchars($row['nombre']) . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary w-full">Guardar Película</button>
            </form>
        </section>

        <!-- Tabla de películas -->
        <section class="animate-fade-in mb-8">
            <h3 class="text-2xl font-bold mb-4">Lista de Películas</h3>
            <table class="w-full border">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="p-2">Título</th>
                        <th class="p-2">Sala</th>
                        <th class="p-2">Horario</th>
                        <th class="p-2">Precio</th>
                        <th class="p-2">Proveedor</th>
                        <th class="p-2">Acciones</th>
                    </tr>
                </thead>
                <tbody id="peliculas-table"></tbody>
            </table>
        </section>

        <!-- Apartado de salas -->
        <section class="animate-fade-in">
            <h3 class="text-2xl font-bold mb-4">Gestionar Salas</h3>
            <table class="w-full border">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="p-2">Sala</th>
                        <th class="p-2">Acciones</th>
                    </tr>
                </thead>
                <tbody id="salas-table"></tbody>
            </table>
        </section>

        <!-- Modal para editar película -->
        <div id="modal" class="modal">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="text-lg font-bold">Editar Película</h2>
                </div>
                <div class="p-4">
                    <form id="edit-pelicula-form">
                        <input type="hidden" id="edit-pelicula_id">
                        <div class="mb-4">
                            <label class="block text-gray-700 mb-2">Título:</label>
                            <input type="text" id="edit-titulo" class="w-full p-3 border rounded-md" required>
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700 mb-2">Sala:</label>
                            <select id="edit-sala" class="w-full p-3 border rounded-md" required>
                                <option value="1">Sala 1</option>
                                <option value="2">Sala 2</option>
                                <option value="3">Sala 3</option>
                                <option value="4">Sala 4</option>
                                <option value="5">Sala 5</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700 mb-2">Horario:</label>
                            <input type="datetime-local" id="edit-horario" class="w-full p-3 border rounded-md" required>
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700 mb-2">Precio:</label>
                            <input type="number" id="edit-precio" step="0.01" class="w-full p-3 border rounded-md" required>
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700 mb-2">Proveedor:</label>
                            <select id="edit-proveedor_id" class="w-full p-3 border rounded-md" required>
                                <?php
                                $query = "SELECT id, nombre FROM proveedores";
                                $result = mysqli_query($conexion, $query);
                                while ($row = mysqli_fetch_assoc($result)) {
                                    echo "<option value='{$row['id']}'>" . htmlspecialchars($row['nombre']) . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-full">Guardar Cambios</button>
                        <button type="button" class="btn btn-secondary w-full mt-2" onclick="closeModal()">Cancelar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showToast(message, isError) {
            const toast = document.createElement('div');
            toast.className = `toast ${isError ? 'error' : 'success'}`;
            toast.textContent = message;
            document.body.appendChild(toast);
            setTimeout(() => toast.classList.add('show'), 100);
            setTimeout(() => toast.remove(), 3000);
        }

        function loadPeliculas() {
            fetch('/cine/api/pelicula.php')
                .then(response => response.json())
                .then(data => {
                    const table = document.getElementById('peliculas-table');
                    table.innerHTML = '';
                    data.forEach(pelicula => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td class="p-2">${pelicula.titulo}</td>
                            <td class="p-2">${pelicula.sala}</td>
                            <td class="p-2">${pelicula.horario}</td>
                            <td class="p-2">$${parseFloat(pelicula.precio).toFixed(2)}</td>
                            <td class="p-2">${pelicula.proveedor}</td>
                            <td class="p-2">
                                <button class="btn btn-secondary" onclick="openModal(${pelicula.id}, '${pelicula.titulo}', '${pelicula.sala}', '${pelicula.horario}', ${pelicula.precio}, ${pelicula.proveedor_id})">Editar</button>
                                <button class="btn btn-danger" onclick="deletePelicula(${pelicula.id})">Eliminar</button>
                            </td>
                        `;
                        table.appendChild(row);
                    });
                })
                .catch(error => showToast('Error al cargar películas', true));
        }

        function loadSalas() {
            fetch('/cine/api/salas.php')
                .then(response => response.json())
                .then(data => {
                    const table = document.getElementById('salas-table');
                    table.innerHTML = '';
                    data.forEach(sala => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td class="p-2">Sala ${sala.sala}</td>
                            <td class="p-2">
                                <button class="btn btn-danger" onclick="liberarAsientos(${sala.sala})">Liberar Asientos</button>
                            </td>
                        `;
                        table.appendChild(row);
                    });
                })
                .catch(error => showToast('Error al cargar salas', true));
        }

        function openModal(id, titulo, sala, horario, precio, proveedor_id) {
            document.getElementById('edit-pelicula_id').value = id;
            document.getElementById('edit-titulo').value = titulo;
            document.getElementById('edit-sala').value = sala;
            document.getElementById('edit-horario').value = horario.replace(' ', 'T').slice(0, 16);
            document.getElementById('edit-precio').value = precio;
            document.getElementById('edit-proveedor_id').value = proveedor_id;
            document.getElementById('modal').style.display = 'flex';
        }

        function closeModal() {
            document.getElementById('modal').style.display = 'none';
        }

        function deletePelicula(id) {
            if (confirm('¿Estás seguro de eliminar esta película?')) {
                fetch('/cine/api/pelicula.php', {
                    method: 'DELETE',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showToast(data.success, false);
                        loadPeliculas();
                    } else {
                        showToast(data.error, true);
                    }
                })
                .catch(error => showToast('Error al eliminar película', true));
            }
        }

        function liberarAsientos(sala) {
            if (confirm(`¿Estás seguro de liberar todos los asientos de la sala ${sala}?`)) {
                fetch('/cine/api/salas.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ sala })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showToast(data.success, false);
                        loadSalas();
                    } else {
                        showToast(data.error, true);
                    }
                })
                .catch(error => showToast('Error al liberar asientos', true));
            }
        }

        document.getElementById('pelicula-form').addEventListener('submit', (e) => {
            e.preventDefault();
            const id = document.getElementById('pelicula_id').value;
            const method = id ? 'PUT' : 'POST';
            const data = {
                id,
                titulo: document.getElementById('titulo').value,
                sala: document.getElementById('sala').value,
                horario: document.getElementById('horario').value,
                precio: parseFloat(document.getElementById('precio').value),
                proveedor_id: parseInt(document.getElementById('proveedor_id').value)
            };

            fetch('/cine/api/pelicula.php', {
                method,
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast(data.success, false);
                    loadPeliculas();
                    document.getElementById('pelicula-form').reset();
                    document.getElementById('pelicula_id').value = '';
                } else {
                    showToast(data.error, true);
                }
            })
            .catch(error => showToast('Error al guardar película', true));
        });

        document.getElementById('edit-pelicula-form').addEventListener('submit', (e) => {
            e.preventDefault();
            const data = {
                id: document.getElementById('edit-pelicula_id').value,
                titulo: document.getElementById('edit-titulo').value,
                sala: document.getElementById('edit-sala').value,
                horario: document.getElementById('edit-horario').value,
                precio: parseFloat(document.getElementById('edit-precio').value),
                proveedor_id: parseInt(document.getElementById('edit-proveedor_id').value)
            };

            fetch('/cine/api/pelicula.php', {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast(data.success, false);
                    loadPeliculas();
                    closeModal();
                } else {
                    showToast(data.error, true);
                }
            })
            .catch(error => showToast('Error al actualizar película', true));
        });

        // Cargar películas y salas al iniciar
        loadPeliculas();
        loadSalas();
    </script>

    <?php
    mysqli_close($conexion);
    require_once '../includes/footer.php';
    ?>
</body>
</html>