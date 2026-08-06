<?php
/**
 * includes/admin_header.php
 * Shared header + sidebar for the admin panel.
 * Expects $pageTitle and $activeAdminPage ('dashboard'|'products'|'orders'|'messages')
 * to be set by the including page.
 */

$pageTitle       = $pageTitle ?? 'Admin - Craftora';
$activeAdminPage = $activeAdminPage ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="<?php echo BASE_URL; ?>css/admin.css" rel="stylesheet">
</head>
<body>

<div class="admin-wrapper">

    <aside class="admin-sidebar">
        <div class="sidebar-brand">
            <i class="fa-solid fa-cube"></i> Craftora <span class="fw-normal small">Admin</span>
        </div>

        <nav class="sidebar-nav">
            <a href="<?php echo BASE_URL; ?>admin/index.php" class="<?php echo $activeAdminPage === 'dashboard' ? 'active' : ''; ?>">
                <i class="fa-solid fa-gauge"></i> Dashboard
            </a>
            <a href="<?php echo BASE_URL; ?>admin/products.php" class="<?php echo $activeAdminPage === 'products' ? 'active' : ''; ?>">
                <i class="fa-solid fa-box"></i> Products
            </a>
            <a href="<?php echo BASE_URL; ?>admin/orders.php" class="<?php echo $activeAdminPage === 'orders' ? 'active' : ''; ?>">
                <i class="fa-solid fa-receipt"></i> Orders
            </a>
            <a href="<?php echo BASE_URL; ?>admin/messages.php" class="<?php echo $activeAdminPage === 'messages' ? 'active' : ''; ?>">
                <i class="fa-solid fa-envelope"></i> Messages
            </a>
        </nav>

        <div class="sidebar-footer">
            <a href="<?php echo BASE_URL; ?>index.php" target="_blank"><i class="fa-solid fa-arrow-up-right-from-square"></i> View Store</a>
            <a href="<?php echo BASE_URL; ?>admin/logout.php" class="text-danger"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
        </div>
    </aside>

    <div class="admin-content">
        <header class="admin-topbar">
            <h5 class="mb-0"><?php echo htmlspecialchars($pageTitle); ?></h5>
            <div class="admin-topbar-user">
                <i class="fa-solid fa-user-shield"></i>
                <?php echo htmlspecialchars($_SESSION['admin_name'] ?? 'Admin'); ?>
            </div>
        </header>

        <main class="admin-main">
