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
    $db = new PDO('sqlite:' . __DIR__ . '/chirp.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Decode JSON input
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Validate input data
    if (empty($data['chirpId']) || empty($data['action'])) {
        echo json_encode(['success' => false, 'error' => 'missing_data']);
        exit;
    }

    // Sanitize and validate inputs
    $chirpId = (int) $data['chirpId'];
    $action = strtolower($data['action']);
    $userId = $_SESSION['user_id'];

    // Validate action and restrict table names
    $validActions = ['like' => 'likes', 'rechirp' => 'rechirps'];
    if (!array_key_exists($action, $validActions)) {
        echo json_encode(['success' => false, 'error' => 'invalid_action']);
        exit;
    }
    $table = $validActions[$action];

    // Check if the user has already interacted
    $stmt = $db->prepare("SELECT COUNT(*) FROM $table WHERE chirp_id = :chirpId AND user_id = :userId");
    $stmt->bindParam(':chirpId', $chirpId, PDO::PARAM_INT);
    $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
    $stmt->execute();
    $exists = $stmt->fetchColumn() > 0;

    if ($exists) {
        // Remove the user's interaction
        $stmt = $db->prepare("DELETE FROM $table WHERE chirp_id = :chirpId AND user_id = :userId");
        $stmt->bindParam(':chirpId', $chirpId, PDO::PARAM_INT);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $status = false;
    } else {
        // Add the user's interaction with current timestamp
        $stmt = $db->prepare("INSERT INTO $table (chirp_id, user_id, timestamp) VALUES (:chirpId, :userId, :timestamp)");
        $stmt->bindParam(':chirpId', $chirpId, PDO::PARAM_INT);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':timestamp', time(), PDO::PARAM_INT);
        $stmt->execute();
        $status = true;
    }

    // Fetch updated count
    $stmt = $db->prepare("SELECT COUNT(*) FROM $table WHERE chirp_id = :chirpId");
    $stmt->bindParam(':chirpId', $chirpId, PDO::PARAM_INT);
    $stmt->execute();
    $count = $stmt->fetchColumn();

    // Prepare response
    $response = [
        'success' => true,
        $action => $status,
        $action . '_count' => (int) $count
    ];

    echo json_encode($response);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
