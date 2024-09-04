<?php
session_start();

header('Content-Type: application/json');

// Check if user is signed in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'not_signed_in']);
    exit;
}

try {
    // Connect to the SQLite database
    $db = new PDO('sqlite:' . __DIR__ . '/../chirp.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Decode JSON input
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Validate input data
    if (empty($data['userId']) || empty($data['action'])) {
        echo json_encode(['success' => false, 'error' => 'missing_data']);
        exit;
    }

    // Sanitize and validate inputs
    $userIdToFollow = (int) $data['userId'];
    $action = strtolower($data['action']);
    $currentUserId = $_SESSION['user_id'];

    // Verify that the userIdToFollow exists
    $stmt = $db->prepare('SELECT COUNT(*) FROM users WHERE id = :userId');
    $stmt->bindParam(':userId', $userIdToFollow, PDO::PARAM_INT);
    $stmt->execute();
    $userExists = $stmt->fetchColumn();

    if (!$userExists) {
        echo json_encode(['success' => false, 'error' => 'user_not_found']);
        exit;
    }

    if ($action === 'follow') {
        // Add a new follow entry
        $stmt = $db->prepare('INSERT INTO following (follower_id, following_id) VALUES (:followerId, :followingId)');
        $stmt->bindParam(':followerId', $currentUserId, PDO::PARAM_INT);
        $stmt->bindParam(':followingId', $userIdToFollow, PDO::PARAM_INT);
        $stmt->execute();

        echo json_encode(['success' => true, 'action' => 'follow']);
    } elseif ($action === 'unfollow') {
        // Remove the follow entry
        $stmt = $db->prepare('DELETE FROM following WHERE follower_id = :followerId AND following_id = :followingId');
        $stmt->bindParam(':followerId', $currentUserId, PDO::PARAM_INT);
        $stmt->bindParam(':followingId', $userIdToFollow, PDO::PARAM_INT);
        $stmt->execute();

        echo json_encode(['success' => true, 'action' => 'unfollow']);
    } else {
        echo json_encode(['success' => false, 'error' => 'invalid_action']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'database_error', 'message' => $e->getMessage()]);
}
?>
