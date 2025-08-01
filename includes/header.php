<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cine</title>
    <!-- Agrega aquí cualquier CSS adicional si es necesario -->
</head>
<body>
    <nav class="bg-gray-800 text-white p-4">
        <div class="max-w-6xl mx-auto">
            <a href="/cine/index.php" class="text-xl font-bold">Cine</a>
            <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin'): ?>
                <a href="/cine/peliculas/administrar.php" class="ml-4">Administrar Películas</a>
                <a href="/cine/golosinas/administrar.php" class="ml-4">Administrar Golosinas</a>
            <?php endif; ?>
            <a href="/cine/boletos/comprar.php" class="ml-4">Comprar Boletos</a>
            <a href="/cine/usuarios/register.php" class="ml-4">Registro</a>
            <?php if (isset($_SESSION['rol'])): ?>
                <a href="/cine/logout.php" class="ml-4">Cerrar Sesión</a>
            <?php endif; ?>
        </div>
    </nav>