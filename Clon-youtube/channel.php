<?php
session_start();
require_once 'config/database.php';

$db = new Database();
$conn = $db->getConnection();

$channel_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Obtener información del canal
$query = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->execute([$channel_id]);
$channel = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$channel) {
    header('Location: index.php');
    exit;
}

// Verificar si está suscrito
$is_subscribed = false;
if(isset($_SESSION['user_id'])) {
    $query = "SELECT id FROM subscriptions WHERE subscriber_id = ? AND channel_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$_SESSION['user_id'], $channel_id]);
    $is_subscribed = $stmt->fetch() ? true : false;
}

// Obtener videos del canal
$query = "SELECT v.*, 
          (SELECT COUNT(*) FROM likes WHERE video_id = v.id AND type = 'like') as likes,
          (SELECT COUNT(*) FROM comments WHERE video_id = v.id) as comments_count
          FROM videos v 
          WHERE v.user_id = ? 
          ORDER BY v.created_at DESC";
$stmt = $conn->prepare($query);
$stmt->execute([$channel_id]);
$videos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($channel['channel_name'] ?? $channel['username']); ?> - YouTube Clone</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="channel-page">
        <?php if($channel['channel_banner']): ?>
        <div class="channel-banner">
            <img src="<?php echo htmlspecialchars($channel['channel_banner']); ?>" alt="Banner">
        </div>
        <?php endif; ?>
        
        <div class="channel-header">
            <div class="channel-info">
                <img src="<?php echo htmlspecialchars($channel['channel_avatar']); ?>" alt="Avatar" class="channel-avatar-xl">
                <div class="channel-details">
                    <h1 class="channel-title"><?php echo htmlspecialchars($channel['channel_name'] ?? $channel['username']); ?></h1>
                    <div class="channel-stats">
                        <span>@<?php echo htmlspecialchars($channel['username']); ?></span>
                        <span>•</span>
                        <span><?php echo number_format($channel['subscribers']); ?> suscriptores</span>
                        <span>•</span>
                        <span><?php echo count($videos); ?> videos</span>
                    </div>
                    <?php if($channel['channel_description']): ?>
                    <p class="channel-description"><?php echo nl2br(htmlspecialchars($channel['channel_description'])); ?></p>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="channel-actions">
                <?php if(isset($_SESSION['user_id']) && $_SESSION['user_id'] == $channel_id): ?>
                    <a href="studio.php" class="btn-primary">
                        <i class="fas fa-edit"></i> Personalizar canal
                    </a>
                <?php elseif(isset($_SESSION['user_id'])): ?>
                    <button class="subscribe-btn-large <?php echo $is_subscribed ? 'subscribed' : ''; ?>" 
                            onclick="toggleSubscribe(<?php echo $channel_id; ?>)">
                        <span><?php echo $is_subscribed ? 'Suscrito' : 'Suscribirse'; ?></span>
                    </button>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="channel-tabs">
            <button class="tab-btn active">Videos</button>
            <button class="tab-btn">Acerca de</button>
        </div>
        
        <div class="channel-content">
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
                        <div class="video-details">
                            <a href="watch.php?v=<?php echo $video['id']; ?>" class="video-title">
                                <?php echo htmlspecialchars($video['title']); ?>
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
            
            <?php if(empty($videos)): ?>
            <div class="empty-state">
                <i class="fas fa-video-slash"></i>
                <p>Este canal aún no tiene videos</p>
            </div>
            <?php endif; ?>
        </div>
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