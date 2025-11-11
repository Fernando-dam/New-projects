<?php
session_start();
require_once 'config/database.php';

if(!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$db = new Database();
$conn = $db->getConnection();

// Obtener playlists del usuario
$query = "SELECT * FROM playlists WHERE user_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($query);
$stmt->execute([$_SESSION['user_id']]);
$playlists = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Biblioteca - YouTube Clone</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="container">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="main-content">
            <h2><i class="fas fa-folder"></i> Biblioteca</h2>
            
            <div class="library-section">
                <h3>Accesos rápidos</h3>
                <div class="quick-links">
                    <a href="history.php" class="quick-link-card">
                        <i class="fas fa-history"></i>
                        <span>Historial</span>
                    </a>
                    <a href="liked.php" class="quick-link-card">
                        <i class="fas fa-thumbs-up"></i>
                        <span>Videos que me gustan</span>
                    </a>
                    <a href="watchlater.php" class="quick-link-card">
                        <i class="fas fa-clock"></i>
                        <span>Ver más tarde</span>
                    </a>
                </div>
            </div>
            
            <div class="library-section">
                <h3>Listas de reproducción</h3>
                <?php if(empty($playlists)): ?>
                <div class="empty-state">
                    <i class="fas fa-list"></i>
                    <p>No tienes listas de reproducción</p>
                </div>
                <?php else: ?>
                <div class="playlists-grid">
                    <?php foreach($playlists as $playlist): ?>
                    <div class="playlist-card">
                        <div class="playlist-thumbnail">
                            <i class="fas fa-list"></i>
                        </div>
                        <div class="playlist-info">
                            <h4><?php echo htmlspecialchars($playlist['name']); ?></h4>
                            <p><?php echo htmlspecialchars($playlist['description']); ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <script src="assets/js/main.js"></script>
    
    <style>
        .library-section {
            margin-bottom: 48px;
        }
        
        .library-section h3 {
            margin-bottom: 16px;
            font-size: 18px;
        }
        
        .quick-links {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 16px;
        }
        
        .quick-link-card {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 20px;
            background-color: var(--secondary-bg);
            border-radius: 12px;
            text-decoration: none;
            color: var(--text-primary);
            transition: background-color 0.2s;
        }
        
        .quick-link-card:hover {
            background-color: var(--hover-bg);
        }
        
        .quick-link-card i {
            font-size: 24px;
        }
        
        .playlists-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 16px;
        }
        
        .playlist-card {
            background-color: var(--secondary-bg);
            border-radius: 12px;
            overflow: hidden;
            cursor: pointer;
        }
        
        .playlist-thumbnail {
            aspect-ratio: 16/9;
            background: linear-gradient(135deg, var(--primary-color), #ff6b6b);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .playlist-thumbnail i {
            font-size: 48px;
            color: white;
        }
        
        .playlist-info {
            padding: 16px;
        }
        
        .playlist-info h4 {
            margin-bottom: 8px;
        }
        
        .playlist-info p {
            color: var(--text-secondary);
            font-size: 14px;
        }
    </style>
</body>
</html>