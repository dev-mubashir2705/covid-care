<?php
session_start();
require_once '../config.php';
require_once '../includes/functions.php';

// Clear all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Set logout message
setFlashMessage('You have been successfully logged out.', 'success');

// Redirect to login page
redirect(SITE_URL . 'patient/login.php');
exit();