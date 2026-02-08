<?php
session_start();

// INFINITYFREE DATABASE CONFIGURATION
define('DB_HOST', 'sql110.infinityfree.com'); // Your InfinityFree hostname
define('DB_USER', 'if0_40773675'); // Your InfinityFree username
define('DB_PASS', 'Osandacom1'); // Your actual InfinityFree MySQL password
define('DB_NAME', 'if0_40773675_ciao_eventi'); // Your database name

// Create connection
try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    // Set charset
    $conn->set_charset("utf8mb4");
    
} catch (Exception $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Get current user ID
function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

// Redirect if not logged in
function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit();
    }
}

// Get user info
function getUserInfo($conn, $user_id) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result ? $result->fetch_assoc() : null;
}

// Helper function for database errors
function dbError($conn) {
    if ($conn->error) {
        return "Database error: " . $conn->error;
    }
    return null;
}
?>