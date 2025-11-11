<?php
session_start();
require_once '../config/database.php';

header('Content-Type: application/json');

if(!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'No autenticado']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$channel_id = $data['channel_id'] ?? 0;

if($_SESSION['user_id'] == $channel_id) {
    echo json_encode(['success' => false, 'message' => 'No puedes suscribirte a tu propio canal']);
    exit;
}

$db = new Database();
$conn = $db->getConnection();

// Verificar si ya está suscrito
$query = "SELECT id FROM subscriptions WHERE subscriber_id = ? AND channel_id = ?";
$stmt = $conn->prepare($query);
$stmt->execute([$_SESSION['user_id'], $channel_id]);
$existing = $stmt->fetch();

$conn->beginTransaction();

try {
    if($existing) {
        // Desuscribirse
        $query = "DELETE FROM subscriptions WHERE subscriber_id = ? AND channel_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$_SESSION['user_id'], $channel_id]);
        
        $query = "UPDATE users SET subscribers = subscribers - 1 WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$channel_id]);
        
        $subscribed = false;
    } else {
        // Suscribirse
        $query = "INSERT INTO subscriptions (subscriber_id, channel_id) VALUES (?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->execute([$_SESSION['user_id'], $channel_id]);
        
        $query = "UPDATE users SET subscribers = subscribers + 1 WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$channel_id]);
        
        $subscribed = true;
    }
    
    $conn->commit();
    
    // Obtener número actualizado de suscriptores
    $query = "SELECT subscribers FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$channel_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'subscribed' => $subscribed,
        'subscribers' => $result['subscribers']
    ]);
    
} catch(Exception $e) {
    $conn->rollBack();
    echo json_encode(['success' => false, 'message' => 'Error al procesar la solicitud']);
}
?>