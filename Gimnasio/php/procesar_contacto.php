<?php
// Configuración
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Verificar que sea una petición POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Método no permitido'
    ]);
    exit;
}

// Obtener datos del formulario
$nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$telefono = isset($_POST['telefono']) ? trim($_POST['telefono']) : '';
$asunto = isset($_POST['asunto']) ? trim($_POST['asunto']) : '';
$mensaje = isset($_POST['mensaje']) ? trim($_POST['mensaje']) : '';

// Validar datos
$errores = [];

if (empty($nombre)) {
    $errores[] = 'El nombre es obligatorio';
}

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errores[] = 'El email no es válido';
}

if (empty($asunto)) {
    $errores[] = 'El asunto es obligatorio';
}

if (empty($mensaje)) {
    $errores[] = 'El mensaje es obligatorio';
}

if (strlen($mensaje) < 10) {
    $errores[] = 'El mensaje debe tener al menos 10 caracteres';
}

// Si hay errores, devolver respuesta
if (!empty($errores)) {
    echo json_encode([
        'success' => false,
        'message' => implode(', ', $errores)
    ]);
    exit;
}

// Conectar a la base de datos
require_once 'config.php';

try {
    $conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8", DB_USER, DB_PASS);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Preparar consulta SQL
    $sql = "INSERT INTO contactos (nombre, email, telefono, asunto, mensaje, fecha_creacion) 
            VALUES (:nombre, :email, :telefono, :asunto, :mensaje, NOW())";
    
    $stmt = $conn->prepare($sql);
    
    // Bind de parámetros
    $stmt->bindParam(':nombre', $nombre);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':telefono', $telefono);
    $stmt->bindParam(':asunto', $asunto);
    $stmt->bindParam(':mensaje', $mensaje);
    
    // Ejecutar consulta
    if ($stmt->execute()) {
        // Enviar email de confirmación (opcional)
        enviarEmailConfirmacion($email, $nombre);
        
        echo json_encode([
            'success' => true,
            'message' => '¡Gracias por contactarnos! Te responderemos pronto.'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Error al guardar el mensaje'
        ]);
    }
    
} catch(PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error de conexión: ' . $e->getMessage()
    ]);
}

// Cerrar conexión
$conn = null;

// Función para enviar email de confirmación
function enviarEmailConfirmacion($emailDestino, $nombre) {
    $asuntoEmail = "Gracias por contactarnos - Fitness Club";
    $mensajeEmail = "
    <html>
    <head>
        <title>Confirmación de contacto</title>
    </head>
    <body>
        <h2>Hola $nombre,</h2>
        <p>Gracias por ponerte en contacto con Fitness Club.</p>
        <p>Hemos recibido tu mensaje y te responderemos lo antes posible.</p>
        <br>
        <p>Saludos,<br>El equipo de Fitness Club</p>
    </body>
    </html>
    ";
    
    // Headers para email HTML
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: noreply@fitnessclub.com" . "\r\n";
    
    // Enviar email
    @mail($emailDestino, $asuntoEmail, $mensajeEmail, $headers);
}
?>