<?php
session_start();

try {
    // Check if the host is allowed
    $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : "none";
    $allowedHosts = ['beta.chirpsocial.net', '127.0.0.1:5500', '192.168.1.230:5500']; // add hosts to the variable as you see fit.
    if ($host === "none" || !in_array($host, $allowedHosts)) {
        header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad Request');
        exit;
    }

    // Check if the user is logged in
    if (!isset($_SESSION['username'])) {
        $_SESSION['error_message'] = "You need to be logged in to post.";
        header('Location: /signin/');
        exit;
    }

    $db = new PDO('sqlite:../chirp.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get the user ID from the users table
    $username = $_SESSION['username'];
    $userStmt = $db->prepare('SELECT id FROM users WHERE username = :username');
    $userStmt->bindParam(':username', $username, PDO::PARAM_STR);
    $userStmt->execute();
    $user = $userStmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        $_SESSION['error_message'] = "User not found.";
        header('Location: /signin/');
        exit;
    }

    $userId = $user['id'];

    // Define rate limiting parameters
    define('MAX_CHARS', 240); // Maximum characters allowed for a chirp

    // Check if the last submission time and attempt count are stored in the session
    $lastSubmissionTime = isset($_SESSION['last_submission_time']) ? $_SESSION['last_submission_time'] : 0;
    $attemptCount = isset($_SESSION['attempt_count']) ? $_SESSION['attempt_count'] : 0;
    $currentTime = time();

    // Calculate cooldown period based on attempt count
    $cooldownSeconds = min(10 + ($attemptCount * 10), 1800); // 10 seconds base, increase by 10s per attempt, max 30 minutes (1800 seconds)

    // Check if cooldown period has elapsed
    if ($currentTime - $lastSubmissionTime < $cooldownSeconds) {
        // Rate limit exceeded, increment attempt count
        $_SESSION['attempt_count'] = ++$attemptCount;
        $_SESSION['error_message'] = "You are posting too quickly. Slow down!";
        header('Location: /');
        exit;
    }

    // Reset attempt count on successful submission
    $_SESSION['attempt_count'] = 0;

    // Check if chirp text is empty or exceeds maximum allowed characters
    $chirpText = trim($_POST['chirpComposeText']);
    if (empty($chirpText)) {
        $_SESSION['error_message'] = "Chirp cannot be empty.";
        header('Location: /');
        exit;
    }

    if (strlen($chirpText) > MAX_CHARS) {
        $_SESSION['error_message'] = "Chirp exceeds maximum character limit of " . MAX_CHARS . " characters.";
        header('Location: /');
        exit;
    }

    // Prepare SQL statement for inserting chirp into database
    $sql = "INSERT INTO chirps (chirp, user, timestamp, isReply) VALUES (:chirp, :user, :timestamp, :isReply)";
    $stmt = $db->prepare($sql);

    // Bind parameters
    $timestamp = time();
    $isReply = 'yes'; // Set isReply to 'yes' for the reply

    $stmt->bindParam(':chirp', $chirpText);
    $stmt->bindParam(':user', $userId);
    $stmt->bindParam(':timestamp', $timestamp);
    $stmt->bindParam(':isReply', $isReply);

    // Execute the SQL statement
    $stmt->execute();

    // Store the ID of the newly inserted chirp
    $chirpId = $db->lastInsertId();

    // Update last submission time in session
    $_SESSION['last_submission_time'] = $currentTime;

    // Retrieve the ID of the main post from query parameters
    $mainPostId = isset($_GET['id']) ? $_GET['id'] : null;

    if ($mainPostId === null) {
        $_SESSION['error_message'] = "Main post ID not provided.";
        header('Location: /');
        exit;
    }

    // Fetch current replies array from the main post
    $fetchStmt = $db->prepare('SELECT replies FROM chirps WHERE id = :id');
    $fetchStmt->bindParam(':id', $mainPostId, PDO::PARAM_INT);
    $fetchStmt->execute();
    $currentReplies = $fetchStmt->fetchColumn();

    // Decode JSON array of replies (assuming 'replies' column is JSON formatted)
    $repliesArray = json_decode($currentReplies);

    // Add the new reply ID to the array
    $repliesArray[] = $chirpId;

    // Encode back to JSON
    $updatedReplies = json_encode($repliesArray);

    // Update the main post with the updated replies array
    $updateStmt = $db->prepare('UPDATE chirps SET replies = :replies WHERE id = :id');
    $updateStmt->bindParam(':replies', $updatedReplies, PDO::PARAM_STR);
    $updateStmt->bindParam(':id', $mainPostId, PDO::PARAM_INT);
    $updateStmt->execute();

    // Redirect to the chirp details page with the chirp ID
    header('Location: /chirp/index.php?id=' . $chirpId);
    exit();

} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}
?>
