<?php
/**
 * COVID-CARE Configuration File
 * Only essential configurations - no duplicate functions
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ============================================
// DATABASE CONFIGURATION
// ============================================

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'covid_system');

// Create database connection
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if (!$conn) {
    die("Database Connection Failed: " . mysqli_connect_error());
}

// Set charset to UTF-8
mysqli_set_charset($conn, "utf8mb4");

// ============================================
// SITE CONFIGURATION
// ============================================

// Timezone
date_default_timezone_set('Asia/Karachi');

// Site URLs and Names
define('SITE_NAME', 'COVID-CARE');
define('SITE_URL', 'http://localhost/covid-care/');

// ============================================
// ESSENTIAL FUNCTIONS (NOT IN FUNCTIONS.PHP)
// ============================================

/**
 * Sanitize input data
 * Only kept here because it needs database connection
 */
function sanitize($data) {
    global $conn;
    return mysqli_real_escape_string($conn, htmlspecialchars(trim($data)));
}

/**
 * Redirect to URL
 * Basic utility function
 */
function redirect($url) {
    header("Location: $url");
    exit();
}

/**
 * Get base URL
 */
function base_url($path = '') {
    return SITE_URL . ltrim($path, '/');
}

// ============================================
// ERROR REPORTING (FOR DEVELOPMENT)
// ============================================

error_reporting(E_ALL);
ini_set('display_errors', 1);

// ============================================
// CHECK FUNCTIONS.PHP IS LOADED
// ============================================

// Define constant to indicate config is loaded
define('CONFIG_LOADED', true);
?>