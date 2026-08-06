<?php
/**
 * config.php
 * Database connection and core app configuration for Craftora.
 */

// ---- Error reporting (disable display_errors in production) ----
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ---- Session ----
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ---- Database configuration ----
define('DB_HOST', 'localhost');
define('DB_NAME', 'Craftora');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// ---- PDO connection ----
$dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (PDOException $e) {
    // Never leak raw DB errors to end users
    die('Database connection failed. Please try again later.');
}

// ---- Base URL / path helpers (adjust if the project moves out of root) ----
define('BASE_URL', '/craftora/');

// ---- Simple auth helper ----
function isLoggedIn(): bool
{
    return isset($_SESSION['user_id']);
}

function currentUserId()
{
    return $_SESSION['user_id'] ?? null;
}

// ---- Simple admin auth helper ----
function isAdminLoggedIn(): bool
{
    return isset($_SESSION['admin_id']);
}

function currentAdminId()
{
    return $_SESSION['admin_id'] ?? null;
}

// ---- Product image path helper ----
// Product images are stored in the DB as bare filenames (e.g. "wallet.jpg").
function productImageUrl($filename)
{
    if (empty($filename)) {
        return BASE_URL . 'images/placeholder.jpg';
    }
    return BASE_URL . 'images/products/' . $filename;
}
