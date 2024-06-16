<?php
session_start();

try {
    /* ===== Dexrn =====
     * This stuff should be turned into an API instead, so you can communicate from the frontend using JS...
     * Especially the header redirect at the bottom... shouldn't the client do that upon OK????
     * Additionally, you could set up API keys, which in my opinion should be free to all, with SOME measures in place to prevent spam and other malicious acts.
     * also I am working on little sleep so some of this code might be shoddy.
    */

    /* ===== Dexrn =====
     * This could very well be not the greatest place to put this (especially with how this codebase is)... but I am gonna put it here, hopefully temporarily.
     * also, there may need to be good sanitization done with this, not too sure though.
    */
    $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : "none";
    $allowedHosts = ['']; // add hosts to the variable as you see fit.
    /* ===== Dexrn =====
     * ^ This should probably be moved to somewhere with a more global scope, if possible.
     * same with the $host var.
    */
    
    if ($host === "none" || !in_array($host, $allowedHosts)) {
        header($_SERVER['SERVER_PROTOCOL'].' 400 Bad Request');
        exit;
    }
    
    $db = new PDO('sqlite:../chirp.db');

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
    $sql = "INSERT INTO chirps (chirp, user, timestamp) VALUES (:chirp, :user, :timestamp)";
    $stmt = $db->prepare($sql);

    // Bind parameters
    $user = '@guest';
    $timestamp = time();

    $stmt->bindParam(':chirp', $chirpText);
    $stmt->bindParam(':user', $user);
    $stmt->bindParam(':timestamp', $timestamp);

    // Execute the SQL statement
    $stmt->execute();

    // Store the ID of the newly inserted chirp
    $chirpId = $db->lastInsertId();

    // Update last submission time in session
    $_SESSION['last_submission_time'] = $currentTime;

    // Redirect to the chirp details page with the chirp ID
    header('Location: /chirp/index.php?id=' . $chirpId);
    exit;
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}
?>