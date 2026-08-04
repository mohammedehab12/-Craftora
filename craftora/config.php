<?php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'Craftora');

// Site Configuration
define('SITE_NAME', 'Craftora');
define('SITE_URL', 'http://localhost/project/Craftora/');

// Start session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Database Connection
try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $conn->set_charset("utf8mb4");
} catch (Exception $e) {
    die("Database connection error: " . $e->getMessage());
}

// Helper Functions
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function getUserId() {
    return $_SESSION['user_id'] ?? null;
}

function redirect($url) {
    header("Location: $url");
    exit();
}

function sanitize($data) {
    return trim($data);
}

function formatPrice($price) {
    return number_format($price, 2) . ' EGP';
}
?>
