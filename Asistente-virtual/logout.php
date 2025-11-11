<?php
require_once 'config.php';

// Destruir todas las variables de sesión
$_SESSION = array();

// Destruir la sesión
session_destroy();

// Redirigir al inicio
showMessage('Sesión cerrada exitosamente', 'success');
redirect('index.php');
?>