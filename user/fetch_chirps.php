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
    $user_id = isset($_GET['user']) ? (int)$_GET['user'] : null;
    $offset = isset($_GET['offset']) ? max((int)$_GET['offset'], 0) : 0;
    $limit = 12;
    $currentUserId = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;

    if ($user_id === null) {
        http_response_code(400); // Bad Request
        echo json_encode(['error' => 'User ID is required.']);
        exit;
    }

    // Fetch posts from the specified user with counts for likes, rechirps, replies, and interactions with current user
    $query = '
        SELECT chirps.*, users.username, users.name, users.profilePic, users.isVerified,
               (SELECT COUNT(*) FROM likes WHERE chirp_id = chirps.id) AS like_count,
               (SELECT COUNT(*) FROM rechirps WHERE chirp_id = chirps.id) AS rechirp_count,
               (SELECT COUNT(*) FROM chirps AS replies WHERE replies.parent = chirps.id AND replies.type = "reply") AS reply_count,
               (SELECT COUNT(*) FROM likes WHERE chirp_id = chirps.id AND user_id = :user_id) AS liked_by_current_user,
               (SELECT COUNT(*) FROM rechirps WHERE chirp_id = chirps.id AND user_id = :user_id) AS rechirped_by_current_user
        FROM chirps
        INNER JOIN users ON chirps.user = users.id
        WHERE chirps.type = "post" AND chirps.user = :user_id
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
        // First, make the links clickable without converting newlines to <br>
        $row['chirp'] = makeLinksClickable(htmlspecialchars($row['chirp'], ENT_NOQUOTES, 'UTF-8'));

        // Normalize all types of newlines to \n (Unix-style line feed)
        $row['chirp'] = str_replace(["\r\n", "\r"], "\n", $row['chirp']);

        // Replace sequences of multiple newlines (\n\n+) with a single newline (\n)
        $row['chirp'] = preg_replace('/\n+/', "\n", $row['chirp']);

        // Convert the newlines (\n) to <br> tags
        $row['chirp'] = nl2br($row['chirp']);

        // Escape other fields for safety (already correct)
        $row['username'] = htmlspecialchars($row['username'], ENT_QUOTES, 'UTF-8');
        $row['name'] = htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8');
        $row['profilePic'] = htmlspecialchars_decode($row['profilePic']);
        $row['isVerified'] = (bool)$row['isVerified'];

        // Add the counts for likes, rechirps, replies, and user interactions
        $row['like_count'] = (int)$row['like_count'];
        $row['rechirp_count'] = (int)$row['rechirp_count'];
        $row['reply_count'] = (int)$row['reply_count'];
        $row['liked_by_current_user'] = (bool)$row['liked_by_current_user'];
        $row['rechirped_by_current_user'] = (bool)$row['rechirped_by_current_user'];

        // Add to chirps array
        $chirps[] = $row;
    }

    // Set JSON header and output
    header('Content-Type: application/json');
    echo json_encode($chirps);

} catch (PDOException $e) {
    // Log error details for debugging purposes
    error_log($e->getMessage());
    http_response_code(500); // Internal Server Error
    echo json_encode(['error' => 'An error occurred while fetching posts. Please try again later.']);
} catch (Exception $e) {
    http_response_code(500); // Internal Server Error
    echo json_encode(['error' => 'An unexpected error occurred.']);
}