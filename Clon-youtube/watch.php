<?php
session_start();
require_once 'config/database.php';

$db = new Database();
$conn = $db->getConnection();

$video_id = isset($_GET['v']) ? intval($_GET['v']) : 0;

// Obtener información del video
$query = "SELECT v.*, u.username, u.channel_name, u.channel_avatar, u.subscribers,
          (SELECT COUNT(*) FROM likes WHERE video_id = v.id AND type = 'like') as likes,
          (SELECT COUNT(*) FROM likes WHERE video_id = v.id AND type = 'dislike') as dislikes
          FROM videos v 
          JOIN users u ON v.user_id = u.id 
          WHERE v.id = ?";
$stmt = $conn->prepare($query);
$stmt->execute([$video_id]);
$video = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$video) {
    header('Location: index.php');
    exit;
}

// Incrementar vistas
$update = "UPDATE videos SET views = views + 1 WHERE id = ?";
$stmt = $conn->prepare($update);
$stmt->execute([$video_id]);

// Verificar si el usuario le dio like/dislike
$user_like = null;
if(isset($_SESSION['user_id'])) {
    $query = "SELECT type FROM likes WHERE user_id = ? AND video_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$_SESSION['user_id'], $video_id]);
    $like = $stmt->fetch(PDO::FETCH_ASSOC);
    $user_like = $like ? $like['type'] : null;
    
    // Verificar suscripción
    $query = "SELECT id FROM subscriptions WHERE subscriber_id = ? AND channel_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$_SESSION['user_id'], $video['user_id']]);
    $is_subscribed = $stmt->fetch() ? true : false;
}

// Obtener comentarios
$query = "SELECT c.*, u.username, u.channel_avatar 
          FROM comments c 
          JOIN users u ON c.user_id = u.id 
          WHERE c.video_id = ? 
          ORDER BY c.created_at DESC";
$stmt = $conn->prepare($query);
$stmt->execute([$video_id]);
$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Videos relacionados
$query = "SELECT v.*, u.channel_name, u.channel_avatar 
          FROM videos v 
          JOIN users u ON v.user_id = u.id 
          WHERE v.id != ? 
          ORDER BY RAND() 
          LIMIT 10";
$stmt = $conn->prepare($query);
$stmt->execute([$video_id]);
$related_videos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($video['title']); ?> - YouTube Clone</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="watch-container">
        <div class="primary-content">
            <div class="video-player">
                <video id="videoPlayer" controls>
                    <source src="<?php echo htmlspecialchars($video['video_path']); ?>" type="video/mp4">
                    Tu navegador no soporta la reproducción de video.
                </video>
            </div>
            
            <div class="video-info-section">
                <h1 class="video-title"><?php echo htmlspecialchars($video['title']); ?></h1>
                
                <div class="video-actions">
                    <div class="video-stats">
                        <span><?php echo number_format($video['views']); ?> vistas</span>
                        <span>•</span>
                        <span><?php echo date('d M Y', strtotime($video['created_at'])); ?></span>
                    </div>
                    
                    <div class="action-buttons">
                        <button class="action-btn <?php echo $user_like === 'like' ? 'active' : ''; ?>" 
                                onclick="likeVideo(<?php echo $video_id; ?>, 'like')">
                            <i class="fas fa-thumbs-up"></i>
                            <span id="likes-count"><?php echo number_format($video['likes']); ?></span>
                        </button>
                        <button class="action-btn <?php echo $user_like === 'dislike' ? 'active' : ''; ?>" 
                                onclick="likeVideo(<?php echo $video_id; ?>, 'dislike')">
                            <i class="fas fa-thumbs-down"></i>
                            <span id="dislikes-count"><?php echo number_format($video['dislikes']); ?></span>
                        </button>
                        <button class="action-btn" onclick="shareVideo()">
                            <i class="fas fa-share"></i>
                            <span>Compartir</span>
                        </button>
                        <button class="action-btn" onclick="downloadVideo()">
                            <i class="fas fa-download"></i>
                            <span>Descargar</span>
                        </button>
                        <button class="action-btn" id="saveBtn" onclick="toggleSaveVideo()">
                            <i class="fas fa-plus"></i>
                            <span>Guardar</span>
                        </button>
                        <?php if(isset($_SESSION['user_id'])): ?>
                        <button class="action-btn" onclick="addToWatchLater(<?php echo $video_id; ?>)">
                            <i class="fas fa-clock"></i>
                            <span>Ver más tarde</span>
                        </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="channel-info">
                <div class="channel-details">
                    <a href="channel.php?id=<?php echo $video['user_id']; ?>">
                        <img src="<?php echo htmlspecialchars($video['channel_avatar']); ?>" alt="" class="channel-avatar-large">
                    </a>
                    <div class="channel-text">
                        <a href="channel.php?id=<?php echo $video['user_id']; ?>" class="channel-name-large">
                            <?php echo htmlspecialchars($video['channel_name'] ?? $video['username']); ?>
                        </a>
                        <span class="subscribers-count" id="subscribersCount"><?php echo number_format($video['subscribers']); ?> suscriptores</span>
                    </div>
                </div>
                
                <?php if(isset($_SESSION['user_id']) && $_SESSION['user_id'] != $video['user_id']): ?>
                <button class="subscribe-btn <?php echo isset($is_subscribed) && $is_subscribed ? 'subscribed' : ''; ?>" 
                        id="subscribeBtn"
                        onclick="toggleSubscribe(<?php echo $video['user_id']; ?>)">
                    <i class="fas fa-bell"></i>
                    <span class="subscribe-text"><?php echo isset($is_subscribed) && $is_subscribed ? 'Suscrito' : 'Suscribirse'; ?></span>
                </button>
                <?php elseif(!isset($_SESSION['user_id'])): ?>
                <button class="subscribe-btn" onclick="window.location.href='login.php'">
                    <span>Suscribirse</span>
                </button>
                <?php endif; ?>
            </div>
            
            <div class="video-description">
                <p><?php echo nl2br(htmlspecialchars($video['description'])); ?></p>
            </div>
            
            <div class="comments-section">
                <h3><?php echo count($comments); ?> comentarios</h3>
                
                <?php if(isset($_SESSION['user_id'])): ?>
                <div class="comment-form">
                    <img src="<?php echo htmlspecialchars($_SESSION['avatar'] ?? 'assets/images/default-avatar.png'); ?>" alt="" class="comment-avatar">
                    <form onsubmit="postComment(event, <?php echo $video_id; ?>)">
                        <input type="text" id="comment-input" placeholder="Añade un comentario..." required>
                        <div class="comment-actions">
                            <button type="button" onclick="document.getElementById('comment-input').value=''">Cancelar</button>
                            <button type="submit">Comentar</button>
                        </div>
                    </form>
                </div>
                <?php endif; ?>
                
                <div id="comments-list">
                    <?php foreach($comments as $comment): ?>
                    <div class="comment">
                        <img src="<?php echo htmlspecialchars($comment['channel_avatar']); ?>" alt="" class="comment-avatar">
                        <div class="comment-content">
                            <div class="comment-header">
                                <span class="comment-author"><?php echo htmlspecialchars($comment['username']); ?></span>
                                <span class="comment-date"><?php echo timeAgo($comment['created_at']); ?></span>
                            </div>
                            <p class="comment-text"><?php echo htmlspecialchars($comment['comment']); ?></p>
                            <div class="comment-actions">
                                <button class="comment-btn">
                                    <i class="fas fa-thumbs-up"></i>
                                    <span><?php echo $comment['likes']; ?></span>
                                </button>
                                <button class="comment-btn">
                                    <i class="fas fa-thumbs-down"></i>
                                </button>
                                <button class="comment-btn">Responder</button>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        
        <div class="secondary-content">
            <h3>Videos relacionados</h3>
            <?php foreach($related_videos as $rel_video): ?>
            <div class="related-video">
                <a href="watch.php?v=<?php echo $rel_video['id']; ?>">
                    <img src="<?php echo htmlspecialchars($rel_video['thumbnail']); ?>" alt="">
                </a>
                <div class="related-video-info">
                    <a href="watch.php?v=<?php echo $rel_video['id']; ?>" class="related-title">
                        <?php echo htmlspecialchars($rel_video['title']); ?>
                    </a>
                    <a href="channel.php?id=<?php echo $rel_video['user_id']; ?>" class="related-channel">
                        <?php echo htmlspecialchars($rel_video['channel_name']); ?>
                    </a>
                    <div class="related-stats">
                        <span><?php echo number_format($rel_video['views']); ?> vistas</span>
                        <span>•</span>
                        <span><?php echo timeAgo($rel_video['created_at']); ?></span>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script src="assets/js/main.js"></script>
    <script src="assets/js/video.js"></script>
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