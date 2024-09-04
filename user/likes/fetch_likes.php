<?php
session_start();

try {
    $db = new PDO('sqlite:' . __DIR__ . '/../../../chirp.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Validate and sanitize inputs
    $user_id = isset($_GET['user']) ? (int)$_GET['user'] : null;
    $offset = isset($_GET['offset']) ? max((int)$_GET['offset'], 0) : 0;
    $limit = 12;
    $currentUserId = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;

    if ($user_id === null) {
        http_response_code(400); // Bad Request
        echo json_encode(['error' => 'User ID is required.']);
        exit;
    }

    // Fetch the IDs of posts that the user has liked
    $likedPostsQuery = '
        SELECT chirp_id
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

    $likedPosts = $likedPostsStmt->fetchAll(PDO::FETCH_COLUMN, 0);

    if (empty($likedPosts)) {
        echo json_encode([]);
        exit;
    }

    // Fetch details of the liked posts
    $chirpIdsPlaceholders = implode(',', array_fill(0, count($likedPosts), '?'));
    $allPostsQuery = "
        SELECT chirps.*, users.username, users.name, users.profilePic, users.isVerified, likes.timestamp as like_timestamp
        FROM chirps
        INNER JOIN users ON chirps.user = users.id
        INNER JOIN likes ON chirps.id = likes.chirp_id
        WHERE chirps.id IN ($chirpIdsPlaceholders) AND likes.user_id = :user_id
        ORDER BY like_timestamp DESC
    ";
    $allPostsStmt = $db->prepare($allPostsQuery);
    foreach ($likedPosts as $index => $chirpId) {
        $allPostsStmt->bindValue($index + 1, $chirpId, PDO::PARAM_INT);
    }
    $allPostsStmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $allPostsStmt->execute();

    $chirps = [];

    while ($row = $allPostsStmt->fetch(PDO::FETCH_ASSOC)) {
        // Sanitize output
        $row['chirp'] = nl2br(htmlspecialchars($row['chirp'], ENT_QUOTES, 'UTF-8'));
        $row['username'] = htmlspecialchars($row['username'], ENT_QUOTES, 'UTF-8');
        $row['name'] = htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8');
        $row['profilePic'] = htmlspecialchars($row['profilePic'], ENT_QUOTES, 'UTF-8');
        $row['isVerified'] = (bool)$row['isVerified'];

        // Fetch counts for likes, rechirps, and replies
        $row['like_count'] = $db->query('SELECT COUNT(*) FROM likes WHERE chirp_id = ' . (int) $row['id'])->fetchColumn();
        $row['rechirp_count'] = $db->query('SELECT COUNT(*) FROM rechirps WHERE chirp_id = ' . (int) $row['id'])->fetchColumn();
        $row['reply_count'] = $db->query('SELECT COUNT(*) FROM chirps WHERE parent = ' . (int) $row['id'] . ' AND type = "reply"')->fetchColumn();

        // Check if the current user liked or rechirped the chirp
        $row['liked_by_current_user'] = (bool) $db->query('SELECT COUNT(*) FROM likes WHERE chirp_id = ' . (int) $row['id'] . ' AND user_id = ' . (int) $currentUserId)->fetchColumn();
        $row['rechirped_by_current_user'] = (bool) $db->query('SELECT COUNT(*) FROM rechirps WHERE chirp_id = ' . (int) $row['id'] . ' AND user_id = ' . (int) $currentUserId)->fetchColumn();

        $chirps[] = $row;
    }

    // Set JSON header and output
    header('Content-Type: application/json');
    echo json_encode($chirps);

} catch (PDOException $e) {
    // Log error details for debugging purposes
    error_log($e->getMessage());
    http_response_code(500); // Internal Server Error
    echo json_encode(['error' => 'An error occurred while fetching liked posts. Please try again later.']);
} catch (Exception $e) {
    http_response_code(500); // Internal Server Error
    echo json_encode(['error' => 'An unexpected error occurred.']);
}
?>
