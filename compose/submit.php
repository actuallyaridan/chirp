<?php
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
