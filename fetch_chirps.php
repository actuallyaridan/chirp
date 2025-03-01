<?php

// List of allowed origins
$allowedOrigins = [
    "https://beta.chirpsocial.net",
    "https://chirpsocial.net",
    "http://legacy.chirpsocial.net",
    "https://legacy.chirpsocial.net"
];

// Get the Origin header from the request
if (isset($_SERVER['HTTP_ORIGIN']) && in_array($_SERVER['HTTP_ORIGIN'], $allowedOrigins)) {
    header("Access-Control-Allow-Origin: " . $_SERVER['HTTP_ORIGIN']);
}

// Allow specific HTTP methods
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

// Allow specific headers
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Handle preflight (OPTIONS) requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

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


// Improved error handling and data fetching logic
try {
    $db = new PDO('sqlite:' . __DIR__ . '/../chirp.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $offset = isset($_GET['offset']) ? max((int)$_GET['offset'], 0) : 0;
    $limit = 12;
    $currentUserId = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;

    $query = '
        SELECT chirps.*, users.username, users.name, users.profilePic, users.isVerified,
               (SELECT COUNT(*) FROM likes WHERE chirp_id = chirps.id) AS like_count,
               (SELECT COUNT(*) FROM rechirps WHERE chirp_id = chirps.id) AS rechirp_count,
               (SELECT COUNT(*) FROM chirps AS replies WHERE replies.parent = chirps.id AND replies.type = "reply") AS reply_count,
               (SELECT COUNT(*) FROM likes WHERE chirp_id = chirps.id AND user_id = :user_id) AS liked_by_current_user,
               (SELECT COUNT(*) FROM rechirps WHERE chirp_id = chirps.id AND user_id = :user_id) AS rechirped_by_current_user
        FROM chirps
        INNER JOIN users ON chirps.user = users.id
        WHERE chirps.type = "post"
        ORDER BY chirps.timestamp DESC
        LIMIT :limit OFFSET :offset';

    $stmt = $db->prepare($query);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->bindValue(':user_id', $currentUserId, PDO::PARAM_INT);
    $stmt->execute();

    $chirps = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // First, make the links clickable without converting newlines to <br>
        $row['chirp'] = makeLinksClickable(htmlspecialchars($row['chirp']));
    
        // Step 1: Normalize all types of newlines to \n (Unix-style line feed)
        $row['chirp'] = str_replace(["\r\n", "\r"], "\n", $row['chirp']);
    
        // Step 2: Replace sequences of multiple newlines (\n\n+) with a single newline (\n)
        $row['chirp'] = preg_replace('/\n+/', "\n", $row['chirp']);
    
        // Step 3: Convert the newlines (\n) to <br> tags
        $row['chirp'] = nl2br($row['chirp']);
    
        // Escape other fields for safety (already correct)
        $row['username'] = htmlspecialchars($row['username']);
        $row['name'] = htmlspecialchars($row['name']);
        $row['profilePic'] = htmlspecialchars_decode($row['profilePic']);
        $row['isVerified'] = (bool)$row['isVerified'];
        $row['liked_by_current_user'] = (bool)$row['liked_by_current_user'];
        $row['rechirped_by_current_user'] = (bool)$row['rechirped_by_current_user'];
    
        // Add to chirps array
        $chirps[] = $row;
    }
    

    header('Content-Type: application/json');
    echo json_encode($chirps);
} catch (PDOException $e) {
    error_log($e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'An error occurred while fetching chirps. Please try again later.']);
}
