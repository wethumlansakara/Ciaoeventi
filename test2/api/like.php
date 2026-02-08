<?php
require_once __DIR__ . '/../config/database.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['error' => 'Please login to like events']);
    exit();
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid request data']);
    exit();
}

$event_id = intval($input['event_id'] ?? 0);
$action = $input['action'] ?? 'like';

if ($event_id <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid event ID']);
    exit();
}

$user_id = getCurrentUserId();

try {
    if ($action === 'like') {
        // Check if already liked
        $check = $conn->prepare("SELECT id FROM likes WHERE user_id = ? AND event_id = ?");
        $check->bind_param("ii", $user_id, $event_id);
        $check->execute();
        $result = $check->get_result();
        
        if ($result->num_rows === 0) {
            // Add like
            $stmt = $conn->prepare("INSERT INTO likes (user_id, event_id) VALUES (?, ?)");
            $stmt->bind_param("ii", $user_id, $event_id);
            
            if ($stmt->execute()) {
                // Update likes count
                $update = $conn->prepare("UPDATE events SET likes_count = likes_count + 1 WHERE id = ?");
                $update->bind_param("i", $event_id);
                $update->execute();
                
                echo json_encode(['success' => true, 'liked' => true]);
            } else {
                throw new Exception('Failed to like event');
            }
        } else {
            // Already liked
            echo json_encode(['success' => true, 'liked' => true]);
        }
    } 
    else if ($action === 'unlike') {
        // Remove like
        $stmt = $conn->prepare("DELETE FROM likes WHERE user_id = ? AND event_id = ?");
        $stmt->bind_param("ii", $user_id, $event_id);
        
        if ($stmt->execute() && $stmt->affected_rows > 0) {
            // Update likes count
            $update = $conn->prepare("UPDATE events SET likes_count = likes_count - 1 WHERE id = ?");
            $update->bind_param("i", $event_id);
            $update->execute();
            
            echo json_encode(['success' => true, 'liked' => false]);
        } else {
            // No like to remove (already unliked)
            echo json_encode(['success' => true, 'liked' => false]);
        }
    } 
    else {
        throw new Exception('Invalid action');
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

$conn->close();
?>