<?php
require_once 'config.php';

// Get featured products
$featured_query = "SELECT * FROM products WHERE featured = TRUE LIMIT 3";
$featured_result = $conn->query($featured_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Craftora - Handmade Crafts, Made With Care</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="display-3 fw-bold mb-4">Handmade Crafts, Made With Care</h1>
                    <p class="lead mb-4">Shop unique handmade products crafted by skilled local artisans. Every piece is made with care and quality you can feel.</p>
                    <div class="d-flex gap-3">
                        <a href="products.php" class="btn btn-primary btn-lg">Shop Now</a>
                        <a href="about.php" class="btn btn-outline-primary btn-lg">Learn More</a>
                    </div>
                </div>
                <div class="col-lg-6">
                    <img src="images/hero-image.jpg" alt="Handmade Crafts" class="img-fluid">
                </div>
            </div>
        </div>
    </section>

    <!-- Highlights -->
    <section class="impact-stats py-5 bg-light">
        <div class="container">
            <div class="row text-center">
                <div class="col-md-4 mb-4">
                    <div class="stat-card">
                        <i class="fas fa-hands fa-3x text-primary mb-3"></i>
                        <h3 class="fw-bold">100%</h3>
                        <p>Handmade Products</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="stat-card">
                        <i class="fas fa-users fa-3x text-primary mb-3"></i>
                        <h3 class="fw-bold">50+</h3>
                        <p>Local Artisans</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="stat-card">
                        <i class="fas fa-star fa-3x text-primary mb-3"></i>
                        <h3 class="fw-bold">1000+</h3>
                        <p>Happy Customers</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Products -->
    <section class="featured-products py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-5 fw-bold">Featured Products</h2>
                <p class="lead text-muted">Handcrafted with love, made to last</p>
            </div>

            <div class="row">
                <?php while($product = $featured_result->fetch_assoc()): ?>
                <div class="col-md-4 mb-4">
                    <div class="card product-card h-100">
                        <img src="images/products/<?php echo htmlspecialchars($product['image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($product['name']); ?>" onerror="this.src='images/placeholder.jpg'">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                            <p class="card-text text-muted"><?php echo substr($product['description'], 0, 80); ?>...</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="h5 mb-0 text-primary"><?php echo formatPrice($product['price']); ?></span>
                                <button class="btn btn-primary add-to-cart" data-id="<?php echo $product['id']; ?>">
                                    <i class="fas fa-shopping-cart"></i> Add to Cart
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>

            <div class="text-center mt-4">
                <a href="products.php" class="btn btn-outline-primary btn-lg">View All Products</a>
            </div>
        </div>
    </section>

    <!-- How It Works -->
    <section class="how-it-works py-5 bg-light">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-5 fw-bold">How It Works</h2>
                <p class="lead text-muted">Simple steps to get your favorite crafts</p>
            </div>

            <div class="row">
                <div class="col-md-3 text-center mb-4">
                    <div class="step-circle">1</div>
                    <h5 class="mt-3">Browse Products</h5>
                    <p class="text-muted">Choose from unique handmade crafts</p>
                </div>
                <div class="col-md-3 text-center mb-4">
                    <div class="step-circle">2</div>
                    <h5 class="mt-3">Add to Cart</h5>
                    <p class="text-muted">Select your favorite items</p>
                </div>
                <div class="col-md-3 text-center mb-4">
                    <div class="step-circle">3</div>
                    <h5 class="mt-3">Checkout</h5>
                    <p class="text-muted">Complete your purchase securely</p>
                </div>
                <div class="col-md-3 text-center mb-4">
                    <div class="step-circle">4</div>
                    <h5 class="mt-3">Enjoy</h5>
                    <p class="text-muted">Receive your handmade product</p>
                </div>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/main.js"></script>
</body>
</html>
