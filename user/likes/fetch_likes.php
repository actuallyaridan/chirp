<?php
session_start();

try {
    $db = new PDO('sqlite:' . __DIR__ . '/../../chirp.db');

    // Get the user ID from the query parameters
    $user_id = isset($_GET['user']) ? (int)$_GET['user'] : null;
    $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
    $limit = 12;
    $currentUserId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null; // Assume user_id is stored in session

    if ($user_id === null) {
        throw new Exception('User ID is required.');
    }

    // Fetch the IDs of posts that the user has liked along with the like timestamp
    $likedPostsQuery = '
        SELECT chirp_id, timestamp
        FROM likes
        WHERE user_id = :user_id
        ORDER BY timestamp DESC
        LIMIT :limit OFFSET :offset
    ';
    $likedPostsStmt = $db->prepare($likedPostsQuery);
    $likedPostsStmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $likedPostsStmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $likedPostsStmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $likedPostsStmt->execute();

    $likedPosts = $likedPostsStmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($likedPosts)) {
        echo json_encode([]);
        exit;
    }

    // Extract the chirp IDs and timestamps
    $chirpIds = array_column($likedPosts, 'chirp_id');
    $timestamps = array_column($likedPosts, 'timestamp');

    // Fetch details of liked posts with timestamps
    $chirpIdsPlaceholders = implode(',', array_fill(0, count($chirpIds), '?'));
    $allPostsQuery = "
        SELECT chirps.*, users.username, users.name, users.profilePic, users.isVerified, likes.timestamp as like_timestamp
        FROM chirps
        INNER JOIN users ON chirps.user = users.id
        INNER JOIN likes ON chirps.id = likes.chirp_id
        WHERE chirps.id IN ($chirpIdsPlaceholders) AND likes.user_id = :user_id
        ORDER BY like_timestamp DESC
    ";
    $allPostsStmt = $db->prepare($allPostsQuery);
    foreach ($chirpIds as $index => $chirpId) {
        $allPostsStmt->bindValue($index + 1, $chirpId, PDO::PARAM_INT);
    }
    $allPostsStmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $allPostsStmt->execute();

    $chirps = [];

    while ($row = $allPostsStmt->fetch(PDO::FETCH_ASSOC)) {
        // Add post details to the result set
        $row['chirp'] = nl2br(htmlspecialchars($row['chirp']));
        $row['username'] = htmlspecialchars($row['username']);
        $row['name'] = htmlspecialchars($row['name']);
        $row['profilePic'] = htmlspecialchars($row['profilePic']);
        $row['isVerified'] = (bool)$row['isVerified'];

        // Fetch counts and current user interactions
        $likesStmt = $db->prepare('SELECT COUNT(*) FROM likes WHERE chirp_id = :chirp_id');
        $likesStmt->bindValue(':chirp_id', $row['id'], PDO::PARAM_INT);
        $likesStmt->execute();
        $row['like_count'] = $likesStmt->fetchColumn();

        $rechirpsStmt = $db->prepare('SELECT COUNT(*) FROM rechirps WHERE chirp_id = :chirp_id');
        $rechirpsStmt->bindValue(':chirp_id', $row['id'], PDO::PARAM_INT);
        $rechirpsStmt->execute();
        $row['rechirp_count'] = $rechirpsStmt->fetchColumn();

        $repliesStmt = $db->prepare('SELECT COUNT(*) FROM chirps WHERE parent = :parent_id AND type = "reply"');
        $repliesStmt->bindValue(':parent_id', $row['id'], PDO::PARAM_INT);
        $repliesStmt->execute();
        $row['reply_count'] = $repliesStmt->fetchColumn();

        // Check if current user liked the chirp
        $likedByUserStmt = $db->prepare('SELECT COUNT(*) FROM likes WHERE chirp_id = :chirp_id AND user_id = :user_id');
        $likedByUserStmt->bindValue(':chirp_id', $row['id'], PDO::PARAM_INT);
        $likedByUserStmt->bindValue(':user_id', $currentUserId, PDO::PARAM_INT);
        $likedByUserStmt->execute();
        $row['liked_by_current_user'] = (bool) $likedByUserStmt->fetchColumn();

        // Check if current user rechirped the chirp
        $rechirpedByUserStmt = $db->prepare('SELECT COUNT(*) FROM rechirps WHERE chirp_id = :chirp_id AND user_id = :user_id');
        $rechirpedByUserStmt->bindValue(':chirp_id', $row['id'], PDO::PARAM_INT);
        $rechirpedByUserStmt->bindValue(':user_id', $currentUserId, PDO::PARAM_INT);
        $rechirpedByUserStmt->execute();
        $row['rechirped_by_current_user'] = (bool) $rechirpedByUserStmt->fetchColumn();

        $chirps[] = $row;
    }

    echo json_encode($chirps);
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
?>
