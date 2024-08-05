<?php
session_start();

try {
    $db = new PDO('sqlite:' . __DIR__ . '/../chirp.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
    $limit = 12;
    $postId = isset($_GET['for']) ? (int)$_GET['for'] : null;
    $currentUserId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null; // Assume user_id is stored in session

    if ($postId === null) {
        http_response_code(400); // Bad Request
        echo json_encode(['error' => 'Post ID is required.']);
        exit;
    }

    // Fetch replies (chirps with the specified parent ID)
    $query = "SELECT chirps.*, users.username, users.name, users.profilePic, users.isVerified 
              FROM chirps 
              INNER JOIN users ON chirps.user = users.id 
              WHERE chirps.parent = :parentId 
              ORDER BY chirps.timestamp DESC 
              LIMIT :limit OFFSET :offset";
    $stmt = $db->prepare($query);

    // Bind parameters
    $stmt->bindValue(':parentId', $postId, PDO::PARAM_INT);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

    $stmt->execute();

    $chirps = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $row['chirp'] = nl2br(htmlspecialchars($row['chirp'], ENT_QUOTES, 'UTF-8'));
        $row['username'] = htmlspecialchars($row['username'], ENT_QUOTES, 'UTF-8');
        $row['name'] = htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8');
        $row['profilePic'] = htmlspecialchars($row['profilePic'], ENT_QUOTES, 'UTF-8');
        $row['isVerified'] = (bool)$row['isVerified'];

        // Decode likes and rechirps
        $likes = json_decode($row['likes'], true) ?: [];
        $rechirps = json_decode($row['rechirps'], true) ?: [];

        // Count likes, rechirps, and replies
        $row['like_count'] = count($likes);
        $row['rechirp_count'] = count($rechirps);
        $row['reply_count'] = count(json_decode($row['replies'], true) ?: []);

        // Check if current user liked or rechirped
        $row['liked_by_current_user'] = $currentUserId && in_array($currentUserId, $likes);
        $row['rechirped_by_current_user'] = $currentUserId && in_array($currentUserId, $rechirps);

        $chirps[] = $row;
    }

    echo json_encode($chirps);
} catch (PDOException $e) {
    http_response_code(500); // Internal Server Error
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(500); // Internal Server Error
    echo json_encode(['error' => 'Error: ' . $e->getMessage()]);
}
?>
