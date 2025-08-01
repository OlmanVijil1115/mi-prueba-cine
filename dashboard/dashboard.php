<?php
// Iniciar la sesión
require_once '../config/conexion.php';

// Verificar si el usuario es administrador
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'admin') {
    header("Location: /cine/index.php#login");
    exit;
}

// Obtener estadísticas
$query = "SELECT COUNT(*) as total FROM peliculas WHERE horario >= NOW()";
$total_peliculas = mysqli_fetch_assoc(mysqli_query($conexion, $query))['total'];
$query = "SELECT COUNT(*) as total FROM golosinas WHERE stock > 0";
$total_golosinas = mysqli_fetch_assoc(mysqli_query($conexion, $query))['total'];
$query = "SELECT COUNT(*) as total FROM facturas";
$total_compras = mysqli_fetch_assoc(mysqli_query($conexion, $query))['total'];

// Incluir el encabezado
require_once '../includes/header.php';
?>

<!-- Contenido principal -->
<div class="max-w-4xl mx-auto py-8 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg">
    <section class="animate-fade-in">
        <h2 class="text-4xl font-bold mb-6 text-center text-gray-800 animate-pulse">Panel de Administración</h2>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
            <div class="bg-white p-6 rounded-lg shadow-lg text-center animate-slide-in">
                <h3 class="text-xl font-bold text-gray-700">Películas Disponibles</h3>
                <p class="text-3xl text-blue