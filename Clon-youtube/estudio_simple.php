<?php
// Activar reporte de errores para debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Iniciar sesión
session_start();

// Verificar autenticación
if(!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Incluir base de datos
try {
    require_once 'config/database.php';
    $db = new Database();
    $conn = $db->getConnection();
} catch(Exception $e) {
    die('Error de conexión: ' . $e->getMessage());
}

// Obtener datos del usuario
try {
    $query = "SELECT * FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if(!$user) {
        die('Usuario no encontrado');
    }
} catch(Exception $e) {
    die('Error al obtener usuario: ' . $e->getMessage());
}

// Obtener estadísticas básicas
$stats = [
    'total_videos' => 0,
    'total_views' => 0,
    'total_likes' => 0,
    'total_comments' => 0
];

try {
    // Videos totales y vistas
    $query = "SELECT COUNT(*) as total_videos, COALESCE(SUM(views), 0) as total_views FROM videos WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$_SESSION['user_id']]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $stats['total_videos'] = $result['total_videos'];
    $stats['total_views'] = $result['total_views'];
    
    // Likes totales
    $query = "SELECT COUNT(*) as total FROM likes WHERE video_id IN (SELECT id FROM videos WHERE user_id = ?) AND type = 'like'";
    $stmt = $conn->prepare($query);
    $stmt->execute([$_SESSION['user_id']]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $stats['total_likes'] = $result['total'];
    
    // Comentarios totales
    $query = "SELECT COUNT(*) as total FROM comments WHERE video_id IN (SELECT id FROM videos WHERE user_id = ?)";
    $stmt = $conn->prepare($query);
    $stmt->execute([$_SESSION['user_id']]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $stats['total_comments'] = $result['total'];
    
} catch(Exception $e) {
    // Si hay error, mantener valores por defecto
}

// Obtener videos del usuario
$videos = [];
try {
    $query = "SELECT v.*, 
              (SELECT COUNT(*) FROM likes WHERE video_id = v.id AND type = 'like') as likes,
              (SELECT COUNT(*) FROM comments WHERE video_id = v.id) as comments_count
              FROM videos v 
              WHERE v.user_id = ? 
              ORDER BY v.created_at DESC 
              LIMIT 10";
    $stmt = $conn->prepare($query);
    $stmt->execute([$_SESSION['user_id']]);
    $videos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(Exception $e) {
    // Si hay error, array vacío
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>YouTube Studio</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .studio-container {
            padding: 80px 20px 20px;
            max-width: 1400px;
            margin: 0 auto;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin: 30px 0;
        }
        .stat-card {
            background: var(--secondary-bg);
            padding: 30px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            gap: 20px;
        }
        .stat-card i {
            font-size: 40px;
            color: var(--primary-color);
        }
        .stat-info h3 {
            font-size: 32px;
            margin-bottom: 5px;
        }
        .stat-info p {
            color: var(--text-secondary);
            font-size: 14px;
        }
        .videos-section {
            margin-top: 40px;
        }
        .video-item {
            background: var(--secondary-bg);
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 15px;
            display: flex;
            gap: 20px;
            align-items: center;
        }
        .video-item img {
            width: 150px;
            aspect-ratio: 16/9;
            border-radius: 8px;
            object-fit: cover;
        }
        .video-item-info {
            flex: 1;
        }
        .video-item-info h3 {
            margin-bottom: 10px;
        }
        .video-item-stats {
            display: flex;
            gap: 20px;
            color: var(--text-secondary);
            font-size: 14px;
        }
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--text-secondary);
        }
        .empty-state i {
            font-size: 64px;
            margin-bottom: 20px;
            display: block;
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="studio-container">
        <h1><i class="fas fa-play-circle"></i> YouTube Studio</h1>
        <p style="color: var(--text-secondary); margin-bottom: 20px;">Panel de control de tu canal</p>
        
        <div class="stats-grid">
            <div class="stat-card">
                <i class="fas fa-eye"></i>
                <div class="stat-info">
                    <h3><?php echo number_format($stats['total_views']); ?></h3>
                    <p>Vistas totales</p>
                </div>
            </div>
            
            <div class="stat-card">
                <i class="fas fa-users"></i>
                <div class="stat-info">
                    <h3><?php echo number_format($user['subscribers']); ?></h3>
                    <p>Suscriptores</p>
                </div>
            </div>
            
            <div class="stat-card">
                <i class="fas fa-video"></i>
                <div class="stat-info">
                    <h3><?php echo $stats['total_videos']; ?></h3>
                    <p>Videos</p>
                </div>
            </div>
            
            <div class="stat-card">
                <i class="fas fa-thumbs-up"></i>
                <div class="stat-info">
                    <h3><?php echo number_format($stats['total_likes']); ?></h3>
                    <p>Me gusta</p>
                </div>
            </div>
        </div>
        
        <div class="videos-section">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2>Tus videos</h2>
                <a href="upload.php" class="btn-primary" style="text-decoration: none;">
                    <i class="fas fa-upload"></i> Subir video
                </a>
            </div>
            
            <?php if(empty($videos)): ?>
            <div class="empty-state">
                <i class="fas fa-video-slash"></i>
                <p>Aún no has subido ningún video</p>
                <a href="upload.php" class="btn-primary" style="display: inline-block; margin-top: 20px; text-decoration: none;">
                    <i class="fas fa-upload"></i> Subir tu primer video
                </a>
            </div>
            <?php else: ?>
                <?php foreach($videos as $video): ?>
                <div class="video-item">
                    <img src="<?php echo htmlspecialchars($video['thumbnail']); ?>" alt="">
                    <div class="video-item-info">
                        <h3><?php echo htmlspecialchars($video['title']); ?></h3>
                        <div class="video-item-stats">
                            <span><i class="fas fa-eye"></i> <?php echo number_format($video['views']); ?> vistas</span>
                            <span><i class="fas fa-thumbs-up"></i> <?php echo number_format($video['likes']); ?></span>
                            <span><i class="fas fa-comment"></i> <?php echo $video['comments_count']; ?></span>
                            <span><i class="fas fa-calendar"></i> <?php echo date('d/m/Y', strtotime($video['created_at'])); ?></span>
                        </div>
                    </div>
                    <a href="watch.php?v=<?php echo $video['id']; ?>" class="btn-secondary" style="text-decoration: none;">
                        <i class="fas fa-eye"></i> Ver
                    </a>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <div style="margin-top: 40px; text-align: center;">
            <a href="index.php" class="btn-secondary" style="text-decoration: none;">
                <i class="fas fa-home"></i> Volver al inicio
            </a>
            <a href="channel.php?id=<?php echo $_SESSION['user_id']; ?>" class="btn-secondary" style="text-decoration: none; margin-left: 10px;">
                <i class="fas fa-user"></i> Ver mi canal
            </a>
        </div>
    </div>
    
    <script src="assets/js/main.js"></script>
</body>
</html>