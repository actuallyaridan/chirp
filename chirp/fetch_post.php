<?php
session_start();

function getDatabaseConnection() {
    try {
        $db = new PDO('sqlite:' . __DIR__ . '/../../chirp.db');
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $db;
    } catch (PDOException $e) {
        die('Database connection failed: ' . htmlspecialchars($e->getMessage()));
    }
}

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


function getChirpDetails($db, $postId) {
    $query = 'SELECT chirps.*, users.username, users.name, users.profilePic, users.isVerified 
              FROM chirps 
              INNER JOIN users ON chirps.user = users.id 
              WHERE chirps.id = :id';
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $postId, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getCounts($db, $postId) {
    $query = 'SELECT 
                (SELECT COUNT(*) FROM likes WHERE chirp_id = :chirp_id) AS like_count,
                (SELECT COUNT(*) FROM rechirps WHERE chirp_id = :chirp_id) AS rechirp_count,
                (SELECT COUNT(*) FROM chirps WHERE parent = :parent_id AND type = "reply") AS reply_count';
    $stmt = $db->prepare($query);
    $stmt->bindParam(':chirp_id', $postId, PDO::PARAM_INT);
    $stmt->bindParam(':parent_id', $postId, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function checkUserInteraction($db, $postId, $currentUserId) {
    $likedStmt = $db->prepare('SELECT COUNT(*) FROM likes WHERE chirp_id = :chirp_id AND user_id = :user_id');
    $likedStmt->bindParam(':chirp_id', $postId, PDO::PARAM_INT);
    $likedStmt->bindParam(':user_id', $currentUserId, PDO::PARAM_INT);
    $likedStmt->execute();
    $liked = $likedStmt->fetchColumn() > 0;

    $rechirpedStmt = $db->prepare('SELECT COUNT(*) FROM rechirps WHERE chirp_id = :chirp_id AND user_id = :user_id');
    $rechirpedStmt->bindParam(':chirp_id', $postId, PDO::PARAM_INT);
    $rechirpedStmt->bindParam(':user_id', $currentUserId, PDO::PARAM_INT);
    $rechirpedStmt->execute();
    $rechirped = $rechirpedStmt->fetchColumn() > 0;

    return [$liked, $rechirped];
}

try {
    $db = getDatabaseConnection();

    // Initialize default values
    $user = "Loading";
    $status = "If this stays here for a prolonged period of time, reload this page.";
    $timestamp = gmdate("Y-m-d\TH:i\Z");

    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        $postId = (int)$_GET['id'];
        $post = getChirpDetails($db, $postId);

        if ($post) {
            $user = htmlspecialchars($post['username'], ENT_QUOTES);
            $profilePic = !empty($post['profilePic']) ? htmlspecialchars($post['profilePic'], ENT_QUOTES) : '/src/images/users/guest/user.svg';
            $name = htmlspecialchars($post['name'], ENT_QUOTES);
            $plainName = $name;

            if ($post['isVerified']) {
                $name .= ' <img class="emoji" src="/src/images/icons/verified.svg" alt="Verified">';
            }

            $title = "$plainName on Chirp: \"" . htmlspecialchars($post['chirp'], ENT_QUOTES) . "\" - Chirp";
            $timestamp = gmdate("Y-m-d\TH:i\Z", $post['timestamp']);

            // Format the chirp text: make links clickable and convert newlines to <br> tags
            $status = makeLinksClickable(htmlspecialchars($post['chirp'], ENT_QUOTES));

            // Normalize and convert newlines to <br> tags
            $status = str_replace(["\r\n", "\r"], "\n", $status); // Normalize line breaks
            $status = preg_replace('/\n+/', "\n", $status); // Replace multiple newlines with a single newline
            $status = nl2br($status); // Convert newlines to <br> tags

            $counts = getCounts($db, $postId);

            $like_count = $counts['like_count'];
            $rechirp_count = $counts['rechirp_count'];
            $reply_count = $counts['reply_count'];

            $liked = false;
            $rechirped = false;

            if (isset($_SESSION['user_id'])) {
                $currentUserId = $_SESSION['user_id'];
                list($liked, $rechirped) = checkUserInteraction($db, $postId, $currentUserId);
            }
        }
    }
} catch (PDOException $e) {
    die('Error: ' . htmlspecialchars($e->getMessage()));
}
