<?php
session_start();

// Destroy the session to log out the user
session_destroy();

// Redirect to the login page or homepage after logout
header("Location: login.php"); // Replace with your desired page
exit();
?>
