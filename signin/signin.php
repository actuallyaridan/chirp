<?php
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
    $username = trim($_POST['username']);
    $password = trim($_POST['pWord']);

    // Validate user input
    if (empty($username) || empty($password)) {
        die('Please fill in both fields.');
    }

    // Connect to the database
    try {
        $db = getDatabaseConnection();

        // Prepare and execute the query
        $stmt = $db->prepare('SELECT * FROM users WHERE username = :username');
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Check if the user exists and the password is correct
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
        echo 'Database error: ' . $e->getMessage();
    }
} else {
    // If the request is not a POST request, redirect to the sign-in page
    header('Location: /signin/');
    exit();
}
?>
