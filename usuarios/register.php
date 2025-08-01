<?php
require_once '../config/conexion.php';
require_once '../includes/header.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrarse</title>
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

        /* Estilos para formularios */
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
        .text-center { text-align: center; }
        .text-gray-700 { color: #4b5563; }
        .text-red-500 { color: #ef4444; }
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
            <h2 class="text-4xl font-bold mb-6 text-center text-gray-800 animate-pulse">Registrarse</h2>
            <form id="register-form" method="POST" class="w-full">
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">Nombre:</label>
                    <input type="text" name="nombre" class="w-full p-3 border rounded-md" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">Correo:</label>
                    <input type="email" name="email" class="w-full p-3 border rounded-md" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">Contraseña:</label>
                    <input type="password" name="password" class="w-full p-3 border rounded-md" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">Membresía:</label>
                    <select name="membresia" class="w-full p-3 border rounded-md" required>
                        <option value="basica">Básica</option>
                        <option value="premium">Premium</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary w-full">Registrarse</button>
            </form>
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

        document.getElementById('register-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            fetch('/cine/api/register.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) throw new Error('Error en la red: ' + response.status);
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    showToast('Registro exitoso. Inicia sesión.', false);
                    setTimeout(() => window.location.href = '/cine/index.php#login', 1000);
                } else {
                    showToast('Error: ' + data.error, true);
                }
            })
            .catch(error => showToast('Error: ' + error.message, true));
        });
    </script>

    <?php
    mysqli_close($conexion);
    require_once '../includes/footer.php';
    ?>
</body>
</html>