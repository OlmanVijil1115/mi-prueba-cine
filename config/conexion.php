<?php
// Iniciar sesión solo si no está activa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Configuración de la conexión a la base de datos
$host = 'localhost';
$usuario = 'root';
$contrasena = '';
$base_datos = 'cine';

$conexion = mysqli_connect($host, $usuario, $contrasena, $base_datos);

if (!$conexion) {
    die('Error de conexión: ' . mysqli_connect_error());
}

// Establecer codificación UTF-8
mysqli_set_charset($conexion, 'utf8');
?>