<?php
session_start();


// Function to check if the image is available with a timeout
function imageExists($url) {
    $context = stream_context_create([
        'http' => [
            'timeout' => 5 // 5 seconds timeout
        ]
    ]);
    $headers = @get_headers($url, 1, $context);
    return $headers && strpos($headers[0], '200') !== false;
}

// Function to make URLs clickable, embed images, and handle mentions, including YouTube embeds
function makeLinksClickable($text) {
    $urlPattern = '/\b((https?:\/\/)?([a-z0-9-]+\.)+[a-z]{2,6}(\/[^\s]*)?(\?[^\s]*)?)/i';

    // Replace URLs with clickable links, images, or YouTube embeds
    $text = preg_replace_callback($urlPattern, function($matches) {
        $url = $matches[1];
        $url = html_entity_decode($url);

        if (strpos($url, 'https://') !== 0 && strpos($url, 'http://') !== 0) {
            $url = 'http://' . $url;
        }

        // Check if the URL is an image link
        $parsedUrl = parse_url($url);
        $path = isset($parsedUrl['path']) ? $parsedUrl['path'] : '';
        $query = isset($parsedUrl['query']) ? $parsedUrl['query'] : '';

        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
        $fileExtension = pathinfo($path, PATHINFO_EXTENSION);

        foreach ($imageExtensions as $extension) {
            if (stripos($query, 'format=' . $extension) !== false) {
                $fileExtension = $extension;
                break;
            }
        }

        if (in_array(strtolower($fileExtension), $imageExtensions)) {
            try {
                if (imageExists($url)) {
                    return '<div class="chirpImageContainer"><img class="imageInChirp" src="' . htmlspecialchars($url, ENT_QUOTES) . '" alt="Photo"></div>';
                } else {
                    return '<div class="chirpsee">🧑‍⚖️ Media not displayed<p class="subText">This image cannot be displayed as it either does not exist or has been removed in response to a legal request or a report by the copyright holder.</p><a class="subText" href="' . htmlspecialchars($url, ENT_QUOTES) . '" target="_blank" rel="noopener noreferrer">Learn more</a></div>';
                }
            } catch (Exception $e) {
                return '<div class="chirpsee">🧑‍⚖️ Media not displayed<p class="subText">This image cannot be displayed due to an error.</p><a class="subText" href="' . htmlspecialchars($url, ENT_QUOTES) . '" target="_blank" rel="noopener noreferrer">Learn more</a></div>';
            }
        } else {
            // Treat as a general clickable URL if not a YouTube or image link
            return '<a class="linkInChirp" href="' . htmlspecialchars($url, ENT_QUOTES) . '" target="_blank" rel="noopener noreferrer">' . htmlspecialchars($url, ENT_QUOTES) . '</a>';
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
    // Set up PDO with error mode to exception
    $db = new PDO('sqlite:' . __DIR__ . '/../../chirp.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Validate and sanitize inputs
    $offset = isset($_GET['offset']) ? max((int)$_GET['offset'], 0) : 0;
    $limit = 12;
    $postId = isset($_GET['for']) ? (int)$_GET['for'] : null;
    $currentUserId = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;

    if ($postId === null) {
        http_response_code(400); // Bad Request
        echo json_encode(['error' => 'Post ID is required.']);
        exit;
    }

    // Single query to fetch replies along with user info and interaction counts
    $query = "
        SELECT chirps.*, users.username, users.name, users.profilePic, users.isVerified,
               (SELECT COUNT(*) FROM likes WHERE chirp_id = chirps.id) AS like_count,
               (SELECT COUNT(*) FROM rechirps WHERE chirp_id = chirps.id) AS rechirp_count,
               (SELECT COUNT(*) FROM chirps AS replies WHERE replies.parent = chirps.id AND replies.type = 'reply') AS reply_count,
               (SELECT COUNT(*) FROM likes WHERE chirp_id = chirps.id AND user_id = :user_id) AS liked_by_current_user,
               (SELECT COUNT(*) FROM rechirps WHERE chirp_id = chirps.id AND user_id = :user_id) AS rechirped_by_current_user
        FROM chirps
        INNER JOIN users ON chirps.user = users.id
        WHERE chirps.parent = :parentId
        ORDER BY chirps.timestamp DESC
        LIMIT :limit OFFSET :offset";

    $stmt = $db->prepare($query);
    $stmt->bindValue(':parentId', $postId, PDO::PARAM_INT);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->bindValue(':user_id', $currentUserId, PDO::PARAM_INT);
    $stmt->execute();

    $chirps = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Sanitize and make links clickable
        $row['chirp'] = makeLinksClickable(htmlspecialchars($row['chirp'], ENT_QUOTES, 'UTF-8'));
        
        // Normalize and convert newlines to <br> tags
        $row['chirp'] = str_replace(["\r\n", "\r"], "\n", $row['chirp']); // Normalize line breaks
        $row['chirp'] = preg_replace('/\n+/', "\n", $row['chirp']); // Replace multiple newlines with a single
        $row['chirp'] = nl2br($row['chirp']); // Convert newlines to <br> tags

        // Escape and sanitize other fields
        $row['username'] = htmlspecialchars($row['username'], ENT_QUOTES, 'UTF-8');
        $row['name'] = htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8');
        $row['profilePic'] = htmlspecialchars_decode($row['profilePic']); // Decode the profilePic URL
        $row['isVerified'] = (bool)$row['isVerified'];

        // Convert interaction counts to booleans for easier use on frontend
        $row['liked_by_current_user'] = (bool)$row['liked_by_current_user'];
        $row['rechirped_by_current_user'] = (bool)$row['rechirped_by_current_user'];

        $chirps[] = $row;
    }

    // Set JSON header and output
    header('Content-Type: application/json');
    echo json_encode($chirps);

} catch (PDOException $e) {
    // Log error details for debugging purposes
    error_log($e->getMessage());
    http_response_code(500); // Set HTTP response code to 500 for server error
    echo json_encode(['error' => 'An error occurred while fetching replies. Please try again later.']);
} catch (Exception $e) {
    // Catch any other non-PDO exceptions
    http_response_code(500); // Set HTTP response code to 500 for server error
    echo json_encode(['error' => 'An unexpected error occurred.']);
}
