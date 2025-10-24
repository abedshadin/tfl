<?php
session_start(); // Ensure session is started

// Check if the user_id session variable is not set
if (!isset($_SESSION['user_id'])) {
    // If not logged in, redirect to login page
    header("Location: login.php");
    exit(); // Stop further script execution
}

// Optional: You can retrieve user details here if needed later on the page
// $current_user_id = $_SESSION['user_id'];
// $current_username = $_SESSION['username']; 
?>