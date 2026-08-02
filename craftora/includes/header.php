<?php
/**
 * includes/header.php
 * Shared header for all Craftora storefront pages.
 *
 * Expected optional variables set by the including page BEFORE this include:
 *   $pageTitle   - string, page-specific title (default: "Craftora")
 *   $activePage  - string, one of: home, products, about, contact, cart, login, register, profile, orders
 *   $extraHead   - string, extra <head> markup (page-specific CSS, meta tags, etc.)
 */

if (!isset($pdo)) {
    require_once __DIR__ . '/../config.php';
}

$pageTitle  = $pageTitle  ?? 'Craftora';
$activePage = $activePage ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Site CSS -->
    <link href="<?php echo BASE_URL; ?>css/style.css" rel="stylesheet">

    <?php if (!empty($extraHead)) echo $extraHead; ?>
</head>
<body>

<header class="site-header">
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm py-3">
        <div class="container">
            <a class="navbar-brand fw-bold" href="<?php echo BASE_URL; ?>index.php">
                <i class="fa-solid fa-cube me-2"></i>Craftora
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item">
                        <a class="nav-link <?php echo $activePage === 'home' ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $activePage === 'products' ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>products.php">Products</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $activePage === 'about' ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>about.php">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $activePage === 'contact' ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>contact.php">Contact</a>
                    </li>
                </ul>

                <ul class="navbar-nav align-items-lg-center gap-lg-2">
                    <li class="nav-item">
                        <a class="nav-link position-relative <?php echo $activePage === 'cart' ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>cart.php">
                            <i class="fa-solid fa-cart-shopping"></i>
                            <span class="badge rounded-pill bg-dark cart-count-badge" id="cartCount">0</span>
                        </a>
                    </li>

                    <?php if (isLoggedIn()): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle <?php echo in_array($activePage, ['profile', 'orders']) ? 'active' : ''; ?>"
                               href="#" id="userMenu" role="button" data-bs-toggle="dropdown">
                                <i class="fa-solid fa-user"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userMenu">
                                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>profile.php">Profile</a></li>
                                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>orders.php">My Orders</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="<?php echo BASE_URL; ?>logout.php">Logout</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="btn btn-outline-dark btn-sm rounded-pill px-3 <?php echo $activePage === 'login' ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>login.php">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-dark btn-sm rounded-pill px-3 <?php echo $activePage === 'register' ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>register.php">Register</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
</header>

<main>
