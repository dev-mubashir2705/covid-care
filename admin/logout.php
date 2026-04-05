<?php
session_start();
require_once '../config.php';
require_once '../includes/functions.php';

// Clear all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Set logout message in session
session_start();
$_SESSION['flash_message'] = 'You have been successfully logged out.';
$_SESSION['flash_type'] = 'success';

// Redirect to login page
header("Location: login.php");
exit();
?>