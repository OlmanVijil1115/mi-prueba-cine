<?php
// Iniciar la sesión
require_once 'config/conexion.php';

// Destruir la sesión
session_unset();
session_destroy();

// Redirigir al login
header("Location: /cine/index.php#login");
exit;
?>