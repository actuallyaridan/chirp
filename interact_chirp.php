<?php
session_start();

header('Content-Type: application/json');

// If someone is signed in, continue; otherwise, stop
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'not_signed_in']);
    exit;
}

try {
    // Connect to the database
    $db = new PDO('sqlite:' . __DIR__ . '/chirp.db');
    $data = json_decode(file_get_contents('php://input'), true);
    $chirpId = $data['chirpId'];
    $action = $data['action'];
    $userId = $_SESSION['user_id'];

    // Determine table and column based on the action
    if ($action === 'like') {
        $table = 'likes';
    } elseif ($action === 'rechirp') {
        $table = 'rechirps';
    } else {
        echo json_encode(['success' => false, 'error' => 'invalid_action']);
        exit;
    }

    // Check if the user has already liked or rechirped
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
        $currentTimestamp = time(); // UNIX timestamp
        $stmt = $db->prepare("INSERT INTO $table (chirp_id, user_id, timestamp) VALUES (:chirpId, :userId, :timestamp)");
        $stmt->bindParam(':chirpId', $chirpId, PDO::PARAM_INT);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':timestamp', $currentTimestamp, PDO::PARAM_INT);
        $stmt->execute();
        $status = true;
    }

    // Fetch updated counts
    $stmt = $db->prepare("SELECT COUNT(*) FROM $table WHERE chirp_id = :chirpId");
    $stmt->bindParam(':chirpId', $chirpId, PDO::PARAM_INT);
    $stmt->execute();
    $count = $stmt->fetchColumn();

    // Prepare response
    $response = [
        'success' => true,
        $action => $status,
        $action . '_count' => $count
    ];

    echo json_encode($response);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
