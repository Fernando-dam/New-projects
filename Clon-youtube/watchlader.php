<?php
session_start();
require_once 'config/database.php';

if(!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$db = new Database();
$conn = $db->getConnection();

// Por ahora mostrar lista vacía (se puede implementar tabla watch_later en el futuro)
$videos = [];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver más tarde - YouTube Clone</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="container">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="main-content">
            <h2><i class="fas fa-clock"></i> Ver más tarde</h2>
            <p style="color: var(--text-secondary); margin-bottom: 24px;">
                Guarda videos para verlos cuando tengas tiempo
            </p>
            
            <div class="empty-state">
                <i class="fas fa-clock"></i>
                <p>Tu lista "Ver más tarde" está vacía</p>
                <p style="font-size: 14px; color: var(--text-secondary);">
                    Haz clic en "Guardar" en cualquier video para agregarlo aquí
                </p>
                <a href="index.php" class="btn-primary" style="display: inline-block; margin-top: 16px; text-decoration: none;">
                    Explorar videos
                </a>
            </div>
        </main>
    </div>

    <script src="assets/js/main.js"></script>
</body>
</html>