<?php
session_start();
require_once 'config/database.php';

$db = new Database();
$conn = $db->getConnection();

$search_query = isset($_GET['q']) ? $_GET['q'] : '';

// Buscar videos
$query = "SELECT v.*, u.username, u.channel_name, u.channel_avatar,
          (SELECT COUNT(*) FROM likes WHERE video_id = v.id AND type = 'like') as likes,
          (SELECT COUNT(*) FROM comments WHERE video_id = v.id) as comments_count
          FROM videos v 
          JOIN users u ON v.user_id = u.id 
          WHERE v.title LIKE ? OR v.description LIKE ? OR v.tags LIKE ?
          ORDER BY v.views DESC, v.created_at DESC 
          LIMIT 50";
$search_term = '%' . $search_query . '%';
$stmt = $conn->prepare($query);
$stmt->execute([$search_term, $search_term, $search_term]);
$videos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($search_query); ?> - Búsqueda - YouTube Clone</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="container">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="main-content">
            <div class="search-results">
                <h2>Resultados de búsqueda para: "<?php echo htmlspecialchars($search_query); ?>"</h2>
                <p class="results-count">Aproximadamente <?php echo count($videos); ?> resultados</p>
                
                <div class="search-results-list">
                    <?php if(empty($videos)): ?>
                    <div class="empty-state">
                        <i class="fas fa-search"></i>
                        <p>No se encontraron resultados para tu búsqueda</p>
                    </div>
                    <?php else: ?>
                        <?php foreach($videos as $video): ?>
                        <div class="search-result-item">
                            <a href="watch.php?v=<?php echo $video['id']; ?>" class="result-thumbnail">
                                <img src="<?php echo htmlspecialchars($video['thumbnail']); ?>" alt="<?php echo htmlspecialchars($video['title']); ?>">
                                <span class="duration"><?php echo $video['duration']; ?></span>
                            </a>
                            <div class="result-info">
                                <a href="watch.php?v=<?php echo $video['id']; ?>" class="result-title">
                                    <?php echo htmlspecialchars($video['title']); ?>
                                </a>
                                <div class="result-meta">
                                    <span><?php echo number_format($video['views']); ?> vistas</span>
                                    <span>•</span>
                                    <span><?php echo timeAgo($video['created_at']); ?></span>
                                </div>
                                <div class="result-channel">
                                    <a href="channel.php?id=<?php echo $video['user_id']; ?>">
                                        <img src="<?php echo htmlspecialchars($video['channel_avatar']); ?>" alt="" class="result-channel-avatar">
                                        <span><?php echo htmlspecialchars($video['channel_name'] ?? $video['username']); ?></span>
                                    </a>
                                </div>
                                <p class="result-description">
                                    <?php echo htmlspecialchars(substr($video['description'], 0, 150)) . '...'; ?>
                                </p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
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

<style>
.search-results {
    max-width: 1200px;
}

.search-results h2 {
    margin-bottom: 8px;
}

.results-count {
    color: var(--text-secondary);
    margin-bottom: 24px;
}

.search-results-list {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.search-result-item {
    display: flex;
    gap: 16px;
}

.result-thumbnail {
    position: relative;
    width: 360px;
    aspect-ratio: 16/9;
    flex-shrink: 0;
    border-radius: 12px;
    overflow: hidden;
}

.result-thumbnail img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.result-info {
    flex: 1;
}

.result-title {
    color: var(--text-primary);
    text-decoration: none;
    font-size: 18px;
    font-weight: 500;
    display: block;
    margin-bottom: 8px;
}

.result-title:hover {
    color: var(--text-primary);
}

.result-meta {
    color: var(--text-secondary);
    font-size: 12px;
    display: flex;
    gap: 4px;
    margin-bottom: 12px;
}

.result-channel {
    margin-bottom: 8px;
}

.result-channel a {
    display: flex;
    align-items: center;
    gap: 8px;
    color: var(--text-secondary);
    text-decoration: none;
    font-size: 12px;
}

.result-channel a:hover {
    color: var(--text-primary);
}

.result-channel-avatar {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    object-fit: cover;
}

.result-description {
    color: var(--text-secondary);
    font-size: 12px;
    line-height: 1.4;
}
</style>