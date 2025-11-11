<?php
// Configuración de la base de datos
define('DB_HOST', 'localhost');
define('DB_NAME', 'gimnasio_salud');
define('DB_USER', 'root');
define('DB_PASS', '');

// Configuración de la aplicación
define('SITE_NAME', 'Fitness Club - Gimnasio Salud');
define('SITE_URL', 'http://localhost/gimnasio_salud/');
define('ADMIN_EMAIL', 'admin@fitnessclub.com');

// Zona horaria
date_default_timezone_set('Europe/Madrid');

// Función para obtener conexión PDO
function getDBConnection() {
    try {
        $conn = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8",
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
        error_log("Error de conexión: " . $e->getMessage());
        return null;
    }
}

// Función para sanitizar datos
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
?>