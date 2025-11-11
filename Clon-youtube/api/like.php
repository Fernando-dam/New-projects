<?php
session_start();
require_once '../config/database.php';

header('Content-Type: application/json');

if(!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'No autenticado']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$video_id = $data['video_id'] ?? 0;
$type = $data['type'] ?? 'like';

if(!in_array($type, ['like', 'dislike'])) {
    echo json_encode(['success' => false, 'message' => 'Tipo inválido']);
    exit;
}

$db = new Database();
$conn = $db->getConnection();

// Verificar si ya existe un like/dislike
$query = "SELECT id, type FROM likes WHERE user_id = ? AND video_id = ?";
$stmt = $conn->prepare($query);
$stmt->execute([$_SESSION['user_id'], $video_id]);
$existing = $stmt->fetch(PDO::FETCH_ASSOC);

if($existing) {
    if($existing['type'] === $type) {
        // Eliminar like/dislike
        $query = "DELETE FROM likes WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$existing['id']]);
    } else {
        // Cambiar tipo
        $query = "UPDATE likes SET type = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$type, $existing['id']]);
    }
} else {
    // Insertar nuevo
    $query = "INSERT INTO likes (user_id, video_id, type) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->execute([$_SESSION['user_id'], $video_id, $type]);
}

// Obtener conteos actualizados
$query = "SELECT 
          (SELECT COUNT(*) FROM likes WHERE video_id = ? AND type = 'like') as likes,
          (SELECT COUNT(*) FROM likes WHERE video_id = ? AND type = 'dislike') as dislikes";
$stmt = $conn->prepare($query);
$stmt->execute([$video_id, $video_id]);
$counts = $stmt->fetch(PDO::FETCH_ASSOC);

echo json_encode([
    'success' => true,
    'likes' => $counts['likes'],
    'dislikes' => $counts['dislikes']
]);
?>