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

    // Fetch replies from the specified user
    $query = '
        SELECT chirps.*, users.username, users.name, users.profilePic, users.isVerified
        FROM chirps
        INNER JOIN users ON chirps.user = users.id
        WHERE chirps.type = "reply" AND chirps.user = :user_id
        ORDER BY chirps.timestamp DESC
        LIMIT :limit OFFSET :offset
    ';
    $stmt = $db->prepare($query);
    $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();

    $chirps = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
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
    echo json_encode(['error' => 'An error occurred while fetching replies. Please try again later.']);
} catch (Exception $e) {
    http_response_code(500); // Internal Server Error
    echo json_encode(['error' => 'An unexpected error occurred.']);
}
?>
