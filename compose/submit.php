<?php
session_start();

header('Content-Type: application/json');

try {
    // Check if the POST data is set
    if (!isset($_POST['chirpComposeText'])) {
        echo json_encode(['error' => "No data was sent with the POST request."]);
        exit;
    }

    // Check if the host is allowed
    $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : "none";
    $allowedHosts = ['beta.chirpsocial.net', 'lambsauce.chirpsocial.net', '127.0.0.1:5500', '192.168.1.230:5500'];
    if ($host === "none" || !in_array($host, $allowedHosts)) {
        echo json_encode(['error' => "Invalid host."]);
        exit;
    }

    // Check if the user is logged in
    if (!isset($_SESSION['username'])) {
        echo json_encode(['error' => "You need to be logged in to post."]);
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
        echo json_encode(['error' => "User not found."]);
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
        echo json_encode(['error' => "You are posting too quickly. Slow down!"]);
        exit;
    }

    // Reset attempt count on successful submission
    $_SESSION['attempt_count'] = 0;

    // Check if chirp text is empty or exceeds maximum allowed characters
    $chirpText = trim($_POST['chirpComposeText']);
    if (empty($chirpText)) {
        echo json_encode(['error' => "Chirp cannot be empty."]);
        exit;
    }

    if (strlen($chirpText) > MAX_CHARS) {
        echo json_encode(['error' => "Chirp exceeds maximum character limit of " . MAX_CHARS . " characters."]);
        exit;
    }

    // Use prepared statements to prevent SQL injection
    $sql = "INSERT INTO chirps (chirp, user, type, parent, timestamp) VALUES (:chirp, :user, 'post', NULL, :timestamp)";
    $stmt = $db->prepare($sql);

    // Bind parameters
    $timestamp = time();
    $stmt->bindParam(':chirp', $chirpText);
    $stmt->bindParam(':user', $userId);
    $stmt->bindParam(':timestamp', $timestamp);

    // Execute the SQL statement
    if ($stmt->execute()) {
        // Store the ID of the newly inserted chirp
        $chirpId = $db->lastInsertId();

        // Update last submission time in session
        $_SESSION['last_submission_time'] = $currentTime;

        // Ensure the location header is set properly
        header('Location: /chirp/index.php?id=' . $chirpId, true, 303);
        exit;
    } else {
        // Execution failed
        echo json_encode(['error' => 'Failed to post chirp.']);
        exit;
    }
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    exit();
} catch (Exception $e) {
    echo json_encode(['error' => 'General error: ' . $e->getMessage()]);
    exit();
}
?>
