<?php
session_start();
require_once 'config/database.php';

if(!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$db = new Database();
$conn = $db->getConnection();

// Obtener videos con like
$query = "SELECT v.*, u.username, u.channel_name, u.channel_avatar, l.created_at as liked_at
          FROM likes l
          JOIN videos v ON l.video_id = v.id
          JOIN users u ON v.user_id = u.id 
          WHERE l.user_id = ? AND l.type = 'like'
          ORDER BY l.created_at DESC";
$stmt = $conn->prepare($query);
$stmt->execute([$_SESSION['user_id']]);
$videos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Videos que me gustan - YouTube Clone</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="container">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="main-content">
            <h2><i class="fas fa-thumbs-up"></i> Videos que me gustan</h2>
            <p style="color: var(--text-secondary); margin-bottom: 24px;">
                <?php echo count($videos); ?> video<?php echo count($videos) != 1 ? 's' : ''; ?>
            </p>
            
            <?php if(empty($videos)): ?>
            <div class="empty-state">
                <i class="fas fa-thumbs-up"></i>
                <p>No has dado like a ningún video</p>
                <p>Los videos que te gusten aparecerán aquí</p>
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