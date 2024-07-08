<?php
session_start();

try {
    $db = new PDO('sqlite:' . __DIR__ . '/../chirp.db');

    $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
    $limit = 12;
    $postId = isset($_GET['for']) ? (int)$_GET['for'] : null;
    $currentUserId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null; // Assume user_id is stored in session

    if ($postId === null) {
        throw new Exception('Post ID is required.');
    }

    // Get the replies JSON array for the given post
    $stmt = $db->prepare('SELECT replies FROM chirps WHERE id = :postId');
    $stmt->bindValue(':postId', $postId, PDO::PARAM_INT);
    $stmt->execute();
    $post = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$post) {
        throw new Exception('Post not found.');
    }

    $repliesArray = json_decode($post['replies'], true);

    if (empty($repliesArray)) {
        echo json_encode([]);
        exit;
    }

    // Fetch the posts whose IDs are in the replies array
    $placeholders = implode(',', array_fill(0, count($repliesArray), '?'));
    $query = "SELECT chirps.*, users.username, users.name, users.profilePic, users.isVerified 
              FROM chirps 
              INNER JOIN users ON chirps.user = users.id 
              WHERE chirps.id IN ($placeholders) 
              ORDER BY chirps.timestamp DESC 
              LIMIT :limit OFFSET :offset";
    $stmt = $db->prepare($query);
    foreach ($repliesArray as $index => $replyId) {
        $stmt->bindValue($index + 1, $replyId, PDO::PARAM_INT);
    }
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
        
        // Decode likes and rechirps
        $likes = json_decode($row['likes'], true);
        $rechirps = json_decode($row['rechirps'], true);

        // Count likes, rechirps, and replies
        $row['like_count'] = count($likes);
        $row['rechirp_count'] = count($rechirps);
        $row['reply_count'] = count(json_decode($row['replies'], true));
        
        // Check if current user liked or rechirped
        $row['liked_by_current_user'] = $currentUserId && in_array($currentUserId, $likes);
        $row['rechirped_by_current_user'] = $currentUserId && in_array($currentUserId, $rechirps);

        $chirps[] = $row;
    }

    echo json_encode($chirps);
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
?>
