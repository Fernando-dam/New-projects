<?php
// Mostrar errores para debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Configuración de la base de datos
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'asistente_virtual');

// Configuración de la aplicación
define('SITE_URL', 'http://localhost/asistente_virtual');
define('SITE_NAME', 'Asistente Virtual - Universidad X');

// Zona horaria
date_default_timezone_set('Europe/Madrid');

// Iniciar sesión
session_start();

// Conexión a la base de datos
try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conn->connect_error) {
        die("Error de conexión: " . $conn->connect_error);
    }
    
    $conn->set_charset("utf8mb4");
} catch (Exception $e) {
    die("Error al conectar con la base de datos: " . $e->getMessage());
}

// Funciones auxiliares
function isLoggedIn() {
    return isset($_SESSION['usuario_id']);
}

function isAdmin() {
    return isset($_SESSION['tipo']) && $_SESSION['tipo'] === 'administrador';
}

function redirect($url) {
    header("Location: $url");
    exit();
}

function sanitize($data) {
    global $conn;
    return $conn->real_escape_string(trim($data));
}

function showMessage($message, $type = 'success') {
    $_SESSION['message'] = $message;
    $_SESSION['message_type'] = $type;
}

function displayMessage() {
    if (isset($_SESSION['message'])) {
        $message = $_SESSION['message'];
        $type = $_SESSION['message_type'];
        echo "<div class='alert alert-$type'>$message</div>";
        unset($_SESSION['message']);
        unset($_SESSION['message_type']);
    }
}
?>