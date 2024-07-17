<?php
// Set session cookie parameters
session_set_cookie_params([
    'lifetime' => 604800, // 7 days in seconds
    'path' => '/', // Will use the same path
    'domain' => '', // Will use the same domain
    'secure' => true, 
    'httponly' => true, // Protects against XSS attacks
    'samesite' => 'Lax'
]);
session_start();

// Send security headers
header('Content-Security-Policy: default-src \'self\'');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');

// Function to connect to the database
function getDatabaseConnection() {
    $db = new PDO('sqlite:../chirp.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $db;
}

// Function to verify the password
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

// Function to sanitize username input
function sanitizeUsername($input) {
    // Remove leading '@' symbol
    if (substr($input, 0, 1) === '@') {
        $input = substr($input, 1);
    }
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Retrieve and sanitize user input
        $username = sanitizeUsername($_POST['username']);
        $password = $_POST['pWord']; // Do not sanitize the password

        // Validate user input
        if (empty($username) || empty($password)) {
            echo json_encode(['error' => 'Please fill in both fields.']);
            exit;
        }

        // Convert username to lowercase for case-insensitive comparison
        $usernameLower = strtolower($username);

        // Connect to the database
        $db = getDatabaseConnection();

        // Prepare and execute the query with case-insensitive comparison
        $stmt = $db->prepare('SELECT * FROM users WHERE LOWER(username) = :username');
        $stmt->bindParam(':username', $usernameLower, PDO::PARAM_STR);
        $stmt->execute();

        // Fetch user data from the database
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Check if the user exists and if the password is correct
        if ($user && verifyPassword($password, $user['password_hash'])) {
            // Regenerate session ID to prevent session fixation attacks
            session_regenerate_id(true);

            // Password is correct, start a new session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['profile_pic'] = $user['profilePic']; // Fetch profile pic path
            $_SESSION['is_verified'] = $user['isVerified']; // Fetch verification status

            // Extend session cookie lifetime
            setcookie(session_name(), session_id(), [
                'expires' => time() + 604800, 
                'path' => '/', 
                'domain' => '', 
                'secure' => true, 
                'httponly' => true, 
                'samesite' => 'Lax'
            ]);

            // Redirect to the home page or user dashboard
            header('Location: /');
            exit();
        } else {
            // Invalid username or password
            echo json_encode(['error' => 'Invalid username or password.']);
            exit();
        }
    } catch (PDOException $e) {
        // Handle database errors
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
        exit();
    }
} else {
    // If the request is not a POST request, redirect to the sign-in page
    header('Location: /signin/');
    exit();
}
?>
