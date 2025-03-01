<?php
session_start();

try {
    // Check if the host is allowed
    $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : "none";
    $allowedHosts = ['beta.chirpsocial.net'];
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

    $db = new PDO('sqlite:' . __DIR__ . '/../../chirp.db');
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
    define('MAX_CHARS', 510); // Maximum characters allowed for a chirp

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
    $chirpText = trim($_POST['replyComposeText']);
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

    // Retrieve the ID of the main post from query parameters
    $mainPostId = isset($_GET['id']) ? intval($_GET['id']) : null;

    if ($mainPostId === null) {
        $_SESSION['error_message'] = "Main post ID not provided.";
        header('Location: /');
        exit;
    }

    // Prepare SQL statement for inserting chirp into database
    $sql = "INSERT INTO chirps (chirp, user, timestamp, type, parent) VALUES (:chirp, :user, :timestamp, 'reply', :parent)";
    $stmt = $db->prepare($sql);

    // Bind parameters
    $timestamp = time();
    $stmt->bindParam(':chirp', $chirpText);
    $stmt->bindParam(':user', $userId);
    $stmt->bindParam(':timestamp', $timestamp);
    $stmt->bindParam(':parent', $mainPostId, PDO::PARAM_INT);

    // Execute the SQL statement
    $stmt->execute();

    // Store the ID of the newly inserted chirp
    $chirpId = $db->lastInsertId();

    // Update last submission time in session
    $_SESSION['last_submission_time'] = $currentTime;

    // Redirect to the chirp details page with the chirp ID
    header('Location: /chirp/index.php?id=' . $chirpId);
    exit();

} catch (PDOException $e) {
    $_SESSION['error_message'] = 'Database error: ' . $e->getMessage();
    header('Location: /');
    exit;
} catch (Exception $e) {
    $_SESSION['error_message'] = 'General error: ' . $e->getMessage();
    header('Location: /');
    exit;
}
?>
