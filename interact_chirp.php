<?php
session_start();

header('Content-Type: application/json');

// If someone is signed in, continute, otherwise stop
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'not_signed_in']);
    exit;
}

try {
    // Connect to the dB
    $db = new PDO('sqlite:' . __DIR__ . '/chirp.db');
    $data = json_decode(file_get_contents('php://input'), true);
    $chirpId = $data['chirpId'];
    $action = $data['action'];
    $userId = $_SESSION['user_id'];

    $column = $action === 'like' ? 'likes' : 'rechirps';

    // Fetch current values
    $stmt = $db->prepare("SELECT $column FROM chirps WHERE id = :chirpId");
    $stmt->bindParam(':chirpId', $chirpId, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    $array = json_decode($result[$column], true) ?: [];

    if (in_array($userId, $array)) {
        // Remove user ID from the array
        $array = array_diff($array, [$userId]);
        $status = false;
    } else {
        // Add user ID to the array
        $array[] = $userId;
        $status = true;
    }

    // Update the dB with new values
    $newArray = json_encode(array_values($array));

    $stmt = $db->prepare("UPDATE chirps SET $column = :newArray WHERE id = :chirpId");
    $stmt->bindParam(':newArray', $newArray, PDO::PARAM_STR);
    $stmt->bindParam(':chirpId', $chirpId, PDO::PARAM_INT);
    $stmt->execute();

    // Fetch updated counts
    $count = count($array);

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
