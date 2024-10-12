<?php
session_start();

// Function to make URLs clickable, embed images, and handle mentions
function makeLinksClickable($text) {
    // Pattern for URLs
    $urlPattern = '/\b((https?:\/\/)?([a-z0-9-]+\.)+[a-z]{2,6}(\/[^\s]*)?(\?[^\s]*)?)/i';

    // Replace URLs with clickable links or images
    $text = preg_replace_callback($urlPattern, function($matches) {
        $url = $matches[1];

        // Parse URL and query string
        $parsedUrl = parse_url($url);
        $path = isset($parsedUrl['path']) ? $parsedUrl['path'] : '';
        $query = isset($parsedUrl['query']) ? $parsedUrl['query'] : '';

        // Strip "https://" and "www." from the display
        $displayUrl = preg_replace('/^https?:\/\/(www\.)?/i', '', $url);

        // Check for image extension in the path or query string
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
        $fileExtension = pathinfo($path, PATHINFO_EXTENSION);

        // Check if the query string contains an image format
        foreach ($imageExtensions as $extension) {
            if (stripos($query, 'format=' . $extension) !== false) {
                $fileExtension = $extension;
                break;
            }
        }

        if (in_array(strtolower($fileExtension), $imageExtensions)) {
            // If it's an image, embed it
            return '<div class="chirpImageContainer"><img class="imageInChirp" src="' . $url . '" alt="Photo"></div>';
        } else {
            // Otherwise, create a clickable link
            return '<a class="linkInChirp" href="' . htmlspecialchars($url, ENT_QUOTES) . '" target="_blank" rel="noopener noreferrer">' . htmlspecialchars($displayUrl, ENT_QUOTES) . '</a>';
        }
    }, $text);

    // Pattern for @mentions, ensuring it doesn't match inside URLs
    $mentionPattern = '/(?<!\S)@([a-zA-Z0-9_]+)(?!\S)/';

    // Replace mentions with clickable profile links
    $text = preg_replace_callback($mentionPattern, function($matches) {
        $username = $matches[1];
        $profileUrl = '/user/?id=' . htmlspecialchars($username, ENT_QUOTES);
        return '<a class="linkInChirp" href="' . $profileUrl . '">@' . htmlspecialchars($username, ENT_QUOTES) . '</a>';
    }, $text);

    return $text;
}


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
        // Sanitize and make links clickable
        $row['chirp'] = makeLinksClickable(nl2br(htmlspecialchars($row['chirp'], ENT_QUOTES, 'UTF-8')));
        $row['username'] = htmlspecialchars($row['username'], ENT_QUOTES, 'UTF-8');
        $row['name'] = htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8');
        $row['profilePic'] = htmlspecialchars($row['profilePic'], ENT_QUOTES, 'UTF-8');
        $row['isVerified'] = (bool)$row['isVerified'];

        // Fetch counts for likes, rechirps, and replies
        $row['like_count'] = (int) $db->query('SELECT COUNT(*) FROM likes WHERE chirp_id = ' . (int) $row['id'])->fetchColumn();
        $row['rechirp_count'] = (int) $db->query('SELECT COUNT(*) FROM rechirps WHERE chirp_id = ' . (int) $row['id'])->fetchColumn();
        $row['reply_count'] = (int) $db->query('SELECT COUNT(*) FROM chirps WHERE parent = ' . (int) $row['id'] . ' AND type = "reply"')->fetchColumn();

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
