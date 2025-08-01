<?php
session_start();
require_once 'config/conexion.php';
require_once 'includes/header.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cine - Bienvenido</title>
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
        .btn-info {
            background-color: #10b981;
            color: white;
        }
        .btn-info:hover {
            background-color: #059669;
        }

        /* Estilos para formularios */
        .text-4xl { font-size: 2.25rem; }
        .text-xl { font-size: 1.25rem; }
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
            <?php if (isset($_SESSION['usuario_id'])): ?>
                <h2 class="text-4xl font-bold mb-6 text-center text-gray-800 animate-pulse">¡Bienvenido, <?php echo htmlspecialchars($_SESSION['nombre']); ?>!</h2>
                <div class="mt-4 text-center">
                    <a href="/cine/boletos/comprar.php" class="btn btn-primary">Comprar Boletos</a>
                    <a href="/cine/golosinas/comprar.php" class="btn btn-secondary">Ver Golosinas</a>
                    <?php if ($_SESSION['rol'] === 'admin'): ?>
                        <a href="/cine/admin/dashboard.php" class="btn btn-info">Panel de Administración</a>
                    <?php endif; ?>
                    <a href="/cine/logout.php" class="btn btn-danger">Cerrar Sesión</a>
                </div>
            <?php else: ?>
                <h2 class="text-4xl font-bold mb-6 text-center text-gray-800 animate-pulse">Iniciar Sesión</h2>
                <form id="login-form" method="POST" action="/cine/usuarios/login.php" class="w-full">
                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2">Correo:</label>
                        <input type="email" name="email" class="w-full p-3 border rounded-md" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2">Contraseña:</label>
                        <input type="password" name="password" class="w-full p-3 border rounded-md" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-full">Iniciar Sesión</button>
                </form>
            <?php endif; ?>
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

        const loginForm = document.getElementById('login-form');
        if (loginForm) {
            loginForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                fetch('/cine/usuarios/login.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    if (!response.ok) throw new Error('Error en la red: ' + response.status);
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        showToast('Inicio de sesión exitoso', false);
                        setTimeout(() => window.location.reload(), 1000);
                    } else {
                        showToast('Error: ' + data.error, true);
                    }
                })
                .catch(error => showToast('Error: ' + error.message, true));
            });
        }
    </script>

    <?php require_once 'includes/footer.php'; ?>
</body>
</html>