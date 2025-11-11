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
$comment = $data['comment'] ?? '';

if(empty(trim($comment))) {
    echo json_encode(['success' => false, 'message' => 'El comentario no puede estar vacío']);
    exit;
}

$db = new Database();
$conn = $db->getConnection();

$query = "INSERT INTO comments (user_id, video_id, comment) VALUES (?, ?, ?)";
$stmt = $conn->prepare($query);

if($stmt->execute([$_SESSION['user_id'], $video_id, $comment])) {
    $comment_id = $conn->lastInsertId();
    
    // Obtener el comentario completo con info del usuario
    $query = "SELECT c.*, u.username, u.channel_avatar 
              FROM comments c 
              JOIN users u ON c.user_id = u.id 
              WHERE c.id = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$comment_id]);
    $new_comment = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'comment' => $new_comment
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al guardar el comentario']);
}
?>