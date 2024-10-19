<?php
session_start();

try {
    // Connect to the SQLite database
    $db = new PDO('sqlite:' . __DIR__ . '/../../../chirp.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Retrieve and sanitize inputs
        $name = trim($_POST['name']);
        $bio = trim($_POST['bio']);
        $userBanner = trim($_POST['userBanner']);
        $profilePic = trim($_POST['profilePic']);
        $userId = $_SESSION['user_id']; // assuming user id is stored in session
        $sessionUsername = $_SESSION['username']; // assuming username is stored in session

        // Validate userBanner and profilePic URLs
        function validateImageUrl($url) {
            // Check if the URL is valid
            if (!filter_var($url, FILTER_VALIDATE_URL)) {
                return false; // Invalid URL format
            }

            // Ensure the URL ends with a valid image extension
            $validExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $urlParts = parse_url($url);
            $path = $urlParts['path'] ?? '';
            $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

            if (!in_array($extension, $validExtensions)) {
                return false; // Invalid file extension
            }

            // Use cURL to check if the URL is being redirected
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_NOBODY, true); // Only fetch headers
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false); // Don't follow redirects
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5); // Set a timeout for the request

            curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $redirectUrl = curl_getinfo($ch, CURLINFO_REDIRECT_URL);
            curl_close($ch);

            // If the response code is not 200, or if a redirect URL is provided, consider it invalid
            if ($httpCode != 200 || !empty($redirectUrl)) {
                return false; // URL is either not accessible or is redirected
            }

            return true; // Valid image URL without redirects
        }

        // Validate the banner URL if provided
        if (!empty($userBanner) && !validateImageUrl($userBanner)) {
            echo "Invalid banner URL. Please provide a valid image URL.";
            exit();
        }

        // Validate the profile picture URL if provided
        if (!empty($profilePic) && !validateImageUrl($profilePic)) {
            echo "Invalid profile picture URL. Please provide a valid image URL.";
            exit();
        }

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
