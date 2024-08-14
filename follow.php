<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'not_signed_in']);
    exit;
}

$userId = filter_input(INPUT_POST, 'user_id', FILTER_SANITIZE_NUMBER_INT);
$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_SPECIAL_CHARS);

if ($userId && $action) {
    try {
        $db = new PDO('sqlite:' . __DIR__ . '/chirp.db');
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        if ($action === 'follow') {
            $stmt = $db->prepare('INSERT INTO following (follower_id, following_id) VALUES (:followerId, :followingId)');
            $stmt->bindParam(':followerId', $_SESSION['user_id']);
            $stmt->bindParam(':followingId', $userId);
            $stmt->execute();
            echo json_encode(['success' => true]);
        } elseif ($action === 'unfollow') {
            $stmt = $db->prepare('DELETE FROM following WHERE follower_id = :followerId AND following_id = :followingId');
            $stmt->bindParam(':followerId', $_SESSION['user_id']);
            $stmt->bindParam(':followingId', $userId);
            $stmt->execute();
            echo json_encode(['success' => true]);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }

    $db = null;
}
?>
