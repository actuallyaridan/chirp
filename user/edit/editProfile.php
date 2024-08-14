<?php
session_start();

try {
    // Connect to the SQLite database
    $db = new PDO('sqlite:' . __DIR__ . '/../../chirp.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Retrieve and sanitize inputs
        $name = trim($_POST['name']);
        $bio = trim($_POST['bio']);
        $userBanner = trim($_POST['userBanner']);
        $profilePic = trim($_POST['profilePic']);
        $userId = $_SESSION['user_id']; // assuming user id is stored in session
        $sessionUsername = $_SESSION['username']; // assuming username is stored in session

        // Fetch the username being edited from the database
        $sql = 'SELECT username FROM users WHERE id = :id';
        $stmt = $db->prepare($sql);
        $stmt->execute([':id' => $userId]);
        $currentUser = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($currentUser) {
            $dbUsername = $currentUser['username'];

            // Check if the session username matches the database username
            if ($sessionUsername !== $dbUsername) {
                // If the usernames don't match, cancel the edit
                header('Location: /error?message=Unauthorized access');
                exit();
            }

            // Strip tags to remove any HTML but allow basic characters
            $name = strip_tags($name);
            $bio = strip_tags($bio);

            // Check if the bio exceeds 140 characters
            if (strlen($bio) > 140) {
                echo "Bio cannot be longer than 140 characters.";
                exit();
            }

            // Fetch the current values from the database
            $sql = 'SELECT name, bio, userBanner, profilePic FROM users WHERE id = :id';
            $stmt = $db->prepare($sql);
            $stmt->execute([':id' => $userId]);
            $currentUser = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($currentUser) {
                // Check if the current values are different from the submitted values
                if ($currentUser['name'] !== $name || $currentUser['bio'] !== $bio || $currentUser['userBanner'] !== $userBanner || $currentUser['profilePic'] !== $profilePic) {
                    // Prepare SQL statement
                    $sql = 'UPDATE users SET name = :name, bio = :bio, userBanner = :userBanner, profilePic = :profilePic WHERE id = :id';
                    $stmt = $db->prepare($sql);

                    // Execute the statement
                    if ($stmt->execute([
                        ':name' => $name,
                        ':bio' => $bio,
                        ':userBanner' => $userBanner,
                        ':profilePic' => $profilePic,
                        ':id' => $userId
                    ])) {
                        // Redirect to the user's profile page
                        header('Location: /user?id=' . urlencode($sessionUsername));
                        exit();
                    } else {
                        echo "Failed to update profile.";
                    }
                } else {
                    // If no changes were made, redirect without updating
                    header('Location: /user?id=' . urlencode($sessionUsername));
                    exit();
                }
            } else {
                echo "User not found.";
            }
        } else {
            echo "User not found.";
        }
    }
} catch (PDOException $e) {
    // Handle any database connection errors
    echo "Database error: " . $e->getMessage();
}
?>
