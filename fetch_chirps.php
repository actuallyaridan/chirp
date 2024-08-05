<?php
session_start();

try {
    $db = new PDO('sqlite:' . __DIR__ . '/chirp.db');

    $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
    $limit = 12;
    $currentUserId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null; // Assume user_id is stored in session

    // Fetch chirps along with user information
    $query = 'SELECT chirps.*, users.username, users.name, users.profilePic, users.isVerified 
              FROM chirps 
              INNER JOIN users ON chirps.user = users.id 
              WHERE chirps.type = "post" 
              ORDER BY chirps.timestamp DESC 
              LIMIT :limit OFFSET :offset';
    $stmt = $db->prepare($query);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();

    $chirps = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
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
}
?>
