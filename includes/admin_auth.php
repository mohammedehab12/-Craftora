<?php
/**
 * includes/admin_auth.php
 * Auth guard for the admin panel. Include this at the very top of
 * every protected admin/*.php page (before any HTML output).
 */

require_once __DIR__ . '/../config.php';

if (!isAdminLoggedIn()) {
    header('Location: ' . BASE_URL . 'admin/login.php');
    exit;
}
