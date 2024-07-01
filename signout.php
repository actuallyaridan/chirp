<?php
session_start();

// Destroy the session
session_unset();
session_destroy();

// Redirect to the sign-in page
header('Location: /');
exit();
?>
