<?php
try {
    $db = new PDO('sqlite:../chirp.db');

    $sql = "INSERT INTO chirps (chirp, user, timestamp) VALUES (:chirp, :user, :timestamp)";

    $stmt = $db->prepare($sql);

    $chirp = $_POST['chirpComposeText'];
    $user = '@guest';
    $timestamp = time();

    $stmt->bindParam(':chirp', $chirp);
    $stmt->bindParam(':user', $user);
    $stmt->bindParam(':timestamp', $timestamp);

    $stmt->execute();

    // Retrieve the ID of the newly inserted chirp
    $chirpId = $db->lastInsertId();

    // Redirect the user to the chirp details page with the chirp ID
    header('Location: /chirp/index.php?id=' . $chirpId);
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}
?>
