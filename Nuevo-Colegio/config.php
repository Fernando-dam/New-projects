<?php
// Configuración de la base de datos
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'colegio_db');

// Configuración de la aplicación
define('SITE_URL', 'http://localhost/colegio');
define('SITE_NAME', 'Colegio Nuevos Horizontes');

// Zona horaria
date_default_timezone_set('Europe/Madrid');

// Iniciar sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Función para conectar a la base de datos
function getDBConnection() {
    try {
        $conn = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]
        );
        return $conn;
    } catch(PDOException $e) {
        die("Error de conexión: " . $e->getMessage());
    }
}

// Función para verificar si el usuario está logueado
function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['user_rol']);
}

// Función para verificar el rol del usuario
function hasRole($roles) {
    if (!is_array($roles)) {
        $roles = [$roles];
    }
    return isLoggedIn() && in_array($_SESSION['user_rol'], $roles);
}

// Función para redireccionar
function redirect($page) {
    header("Location: $page");
    exit();
}

// Función para sanitizar entradas
function cleanInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Función para formatear fecha
function formatDate($date) {
    return date('d/m/Y', strtotime($date));
}

// Función para formatear moneda
function formatCurrency($amount) {
    return '€' . number_format($amount, 2, ',', '.');
}
?>