<?php
session_start();
require_once 'config/database.php';

$db = new Database();
$conn = $db->getConnection();

$category = isset($_GET['c']) ? $_GET['c'] : 'music';

$categories = [
    'music' => ['name' => 'Música', 'icon' => 'fa-music'],
    'sports' => ['name' => 'Deportes', 'icon' => 'fa-futbol'],
    'gaming' => ['name' => 'Videojuegos', 'icon' => 'fa-gamepad'],
    'news' => ['name' => 'Noticias', 'icon' => 'fa-newspaper'],
    'live' => ['name' => 'En directo', 'icon' => 'fa-broadcast-tower'],
    'entertainment' => ['name' => 'Entretenimiento', 'icon' => 'fa-star'],
    'education' => ['name' => 'Educación', 'icon' => 'fa-graduation-cap'],
    'science' => ['name' => 'Ciencia y Tecnología', 'icon' => 'fa-flask'],
    'travel' => ['name' => 'Viajes', 'icon' => 'fa-plane'],
    'cooking' => ['name' => 'Cocina', 'icon' => 'fa-utensils']
];

$current_category = $categories[$category] ?? $categories['music'];

// Obtener videos de la categoría
$query = "SELECT v.*, u.username, u.channel_name, u.channel_avatar,
          (SELECT COUNT(*) FROM likes WHERE video_id = v.id AND type = 'like') as likes
          FROM videos v 
          JOIN users u ON v.user_id = u.id 
          WHERE v.category = ?
          ORDER BY v.created_at DESC 
          LIMIT 50";
$stmt = $conn->prepare($query);
$stmt->execute([$category]);
$videos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $current_category['name']; ?> - YouTube Clone</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="container">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="main-content">
            <h2><i class="fas <?php echo $current_category['icon']; ?>"></i> <?php echo $current_category['name']; ?></h2>
            
            <?php if(empty($videos)): ?>
            <div class="empty-state">
                <i class="fas fa-video-slash"></i>
                <p>No hay videos en esta categoría</p>
            </div>
            <?php else: ?>
            <div class="videos-grid">
                <?php foreach($videos as $video): ?>
                <div class="video-card">
                    <a href="watch.php?v=<?php echo $video['id']; ?>">
                        <div class="thumbnail">
                            <img src="<?php echo htmlspecialchars($video['thumbnail']); ?>" alt="<?php echo htmlspecialchars($video['title']); ?>">
                            <span class="duration"><?php echo $video['duration']; ?></span>
                        </div>
                    </a>
                    <div class="video-info">
                        <a href="channel.php?id=<?php echo $video['user_id']; ?>">
                            <img src="<?php echo htmlspecialchars($video['channel_avatar']); ?>" alt="" class="channel-avatar">
                        </a>
                        <div class="video-details">
                            <a href="watch.php?v=<?php echo $video['id']; ?>" class="video-title">
                                <?php echo htmlspecialchars($video['title']); ?>
                            </a>
                            <a href="channel.php?id=<?php echo $video['user_id']; ?>" class="channel-name">
                                <?php echo htmlspecialchars($video['channel_name'] ?? $video['username']); ?>
                            </a>
                            <div class="video-stats">
                                <span><?php echo number_format($video['views']); ?> vistas</span>
                                <span>•</span>
                                <span><?php echo timeAgo($video['created_at']); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </main>
    </div>

    <script src="assets/js/main.js"></script>
</body>
</html>

<?php
function timeAgo($datetime) {
    $time = strtotime($datetime);
    $diff = time() - $time;
    
    if($diff < 60) return 'Hace ' . $diff . ' segundos';
    if($diff < 3600) return 'Hace ' . floor($diff/60) . ' minutos';
    if($diff < 86400) return 'Hace ' . floor($diff/3600) . ' horas';
    if($diff < 604800) return 'Hace ' . floor($diff/86400) . ' días';
    if($diff < 2592000) return 'Hace ' . floor($diff/604800) . ' semanas';
    if($diff < 31536000) return 'Hace ' . floor($diff/2592000) . ' meses';
    return 'Hace ' . floor($diff/31536000) . ' años';
}
?>