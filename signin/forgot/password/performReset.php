<?php
session_start();

try {
    // Connect to the database
    $db = new PDO('sqlite:' . __DIR__ . '/../../../../chirp.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Collect inputs
        $username = $_POST['usernameResetPassword'];
        $inviteCode = strtoupper($_POST['inviteResetPassword']); // Convert invite code to uppercase
        $password = $_POST['pword'];
        $passwordConfirm = $_POST['pwordConfirm'];

        // Validate username: letters, numbers, and underscores allowed
        if (!preg_match('/^[A-Za-z0-9_]+$/', $username)) {
            echo json_encode(['error' => 'Invalid username. Only letters, numbers, and underscores are allowed.']);
            exit;
        }

        // Validate password match
        if ($password !== $passwordConfirm) {
            echo json_encode(['error' => 'Passwords do not match']);
            exit;
        }

        // Fetch invite details and ensure it is reserved for the given username
        $stmt = $db->prepare("SELECT id, reservedFor FROM invites WHERE UPPER(invite) = :inviteCode");
        $stmt->execute(['inviteCode' => $inviteCode]);
        $invite = $stmt->fetch(PDO::FETCH_ASSOC);

        // Check if invite code exists
        if (!$invite) {
            echo json_encode(['error' => 'Invalid invite code']);
            exit;
        }

        // Check if the invite is reserved for the specific username
        if ($invite['reservedFor'] !== null && strtolower($invite['reservedFor']) !== strtolower($username)) {
            echo json_encode(['error' => 'Invite not reserved for this username']);
            exit;
        }

        // Check if the username exists
        $stmt = $db->prepare("SELECT id, password_hash FROM users WHERE LOWER(username) = LOWER(:username) AND usedInvite = :inviteCode");
        $stmt->execute(['username' => $username, 'inviteCode' => $inviteCode]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            echo json_encode(['error' => 'Username and invite code do not match']);
            exit;
        }

        // Hash the new password
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        // Update the user's password in the database
        $stmt = $db->prepare("UPDATE users SET password_hash = :passwordHash WHERE id = :userId");
        $stmt->execute(['passwordHash' => $passwordHash, 'userId' => $user['id']]);

        // Successfully reset password
        echo json_encode(['success' => true]);
        exit;
    }
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    exit;
}
?>
