<?php
session_start();
require_once 'config/database.php';

if(!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$db = new Database();
$conn = $db->getConnection();

// Obtener historial de videos vistos
$query = "SELECT v.*, u.username, u.channel_name, u.channel_avatar, wh.watched_at,
          (SELECT COUNT(*) FROM likes WHERE video_id = v.id AND type = 'like') as likes
          FROM watch_history wh
          JOIN videos v ON wh.video_id = v.id
          JOIN users u ON v.user_id = u.id 
          WHERE wh.user_id = ?
          ORDER BY wh.watched_at DESC 
          LIMIT 50";
$stmt = $conn->prepare($query);
$stmt->execute([$_SESSION['user_id']]);
$videos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial - YouTube Clone</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="container">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="main-content">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                <h2><i class="fas fa-history"></i> Historial</h2>
                <?php if(!empty($videos)): ?>
                <button class="btn-secondary" onclick="clearHistory()">
                    <i class="fas fa-trash"></i> Borrar historial
                </button>
                <?php endif; ?>
            </div>
            
            <?php if(empty($videos)): ?>
            <div class="empty-state">
                <i class="fas fa-history"></i>
                <p>Tu historial está vacío</p>
                <p>Los videos que veas aparecerán aquí</p>
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
                                <span>Visto <?php echo timeAgo($video['watched_at']); ?></span>
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
    <script>
        function clearHistory() {
            if(confirm('¿Estás seguro de que quieres borrar todo tu historial?')) {
                // Implementar limpieza de historial
                alert('Función de borrar historial pendiente de implementar');
            }
        }
    </script>
</body>
</html>

<?php
function timeAgo($datetime) {
    $time = strtotime($datetime);
    $diff = time() - $time;
    
    if($diff < 60) return 'hace ' . $diff . ' segundos';
    if($diff < 3600) return 'hace ' . floor($diff/60) . ' minutos';
    if($diff < 86400) return 'hace ' . floor($diff/3600) . ' horas';
    if($diff < 604800) return 'hace ' . floor($diff/86400) . ' días';
    if($diff < 2592000) return 'hace ' . floor($diff/604800) . ' semanas';
    if($diff < 31536000) return 'hace ' . floor($diff/2592000) . ' meses';
    return 'hace ' . floor($diff/31536000) . ' años';
}
?>