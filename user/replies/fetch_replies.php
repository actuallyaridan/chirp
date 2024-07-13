<?php
session_start();

try {
    $db = new PDO('sqlite:' . __DIR__ . '/../../chirp.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
    $limit = 12;
    $user_id = isset($_GET['user']) ? (int)$_GET['user'] : null;

    if (!$user_id) {
        throw new Exception('User ID not provided.');
    }

    $query = 'SELECT chirps.*, users.username, users.name, users.profilePic, users.isVerified 
              FROM chirps 
              INNER JOIN users ON chirps.user = users.id 
              WHERE chirps.user = :user_id 
              AND chirps.isReply = "yes" 
              ORDER BY chirps.timestamp DESC 
              LIMIT :limit OFFSET :offset';
    $stmt = $db->prepare($query);
    $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
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
        
        // Count likes, rechirps, and replies
        $likes = json_decode($row['likes'], true);
        $rechirps = json_decode($row['rechirps'], true);

        $row['like_count'] = count($likes);
        $row['rechirp_count'] = count($rechirps);
        $row['reply_count'] = count(json_decode($row['replies'], true));
        
        // Check if current user liked or rechirped
        $currentUserId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
        $row['liked_by_current_user'] = $currentUserId && in_array($currentUserId, $likes);
        $row['rechirped_by_current_user'] = $currentUserId && in_array($currentUserId, $rechirps);

        $chirps[] = $row;
    }

    echo json_encode($chirps);
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
} catch (Exception $e) {
    echo $e->getMessage();
}
?>
