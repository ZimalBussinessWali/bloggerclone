<?php
/**
 * Database Connection and Session Initialization
 */

$host = 'localhost';
$dbname = 'rsk80_39';
$username = 'rsk80_39';
$password = '123456';

try {
    // Create PDO connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    
    // Set error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Set default fetch mode to associative array
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    // In production, you might want to log this instead of displaying it
    die("Database connection failed. Please ensure the database is set up and credentials are correct.");
}

// Start user session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Utility function to sanitize output
 */
function h($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Check if user is logged in
 */
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

/**
 * Redirect to login if not authenticated
 */
function require_login() {
    if (!is_logged_in()) {
        header("Location: login.php");
        exit();
    }
}
?>
