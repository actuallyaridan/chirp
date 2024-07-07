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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and trim user input
    $username = trim($_POST['username']);
    $password = trim($_POST['pWord']);

    // Validate user input
    if (empty($username) || empty($password)) {
        die('Please fill in both fields.');
    }

    // Convert username to lowercase for case-insensitive comparison
    $usernameLower = strtolower($username);

    // Connect to the database
    try {
        $db = getDatabaseConnection();

        // Prepare and execute the query with case-insensitive comparison
        $stmt = $db->prepare('SELECT * FROM users WHERE LOWER(username) = :username');
        $stmt->bindParam(':username', $usernameLower, PDO::PARAM_STR);
        $stmt->execute();

        // Fetch user data from the database
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Check if the user exists and if the password is correct
        if ($user && verifyPassword($password, $user['password_hash'])) {
            // Password is correct, start a new session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['profile_pic'] = $user['profilePic']; // Fetch profile pic path
            $_SESSION['is_verified'] = $user['isVerified']; // Fetch verification status
            
            // Redirect to the home page or user dashboard
            header('Location: /');
            exit();
        } else {
            // Invalid username or password
            echo 'Invalid username or password.';
        }
    } catch (PDOException $e) {
        // Handle database errors
        echo 'Database error: ' . $e->getMessage();
    }
} else {
    // If the request is not a POST request, redirect to the sign-in page
    header('Location: /signin/');
    exit();
}
?>
