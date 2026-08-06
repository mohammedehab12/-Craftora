<?php
require_once __DIR__ . '/config.php';

$stmt = $pdo->prepare('SELECT * FROM products WHERE featured = 1 ORDER BY created_at DESC LIMIT 6');
$stmt->execute();
$featuredProducts = $stmt->fetchAll();

$pageTitle  = 'Craftora - Handmade Goods';
$activePage = 'home';
include __DIR__ . '/includes/header.php';
?>

<section class="hero" style="background-image: url('images/hero-image.jpg');">
    <div class="container">
        <h1 class="mb-3">Handmade with Heart</h1>
        <p class="mb-4">Discover unique, handcrafted pieces made by artisans — for your home, your style, your everyday life.</p>
        <a href="products.php" class="btn btn-gradient btn-lg px-4">Shop Now</a>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <h2 class="section-title">Featured Products</h2>

        <?php if (empty($featuredProducts)): ?>
            <div class="empty-state">
                <i class="fa-solid fa-box-open"></i>
                <p>No featured products yet. Check back soon!</p>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($featuredProducts as $product): ?>
                    <div class="col-6 col-md-4 col-lg-2">
                        <div class="product-card">
                            <img src="<?php echo htmlspecialchars(productImageUrl($product['image'])); ?>"
                                 alt="<?php echo htmlspecialchars($product['name']); ?>" class="product-img">
                            <div class="product-body">
                                <div class="product-category"><?php echo htmlspecialchars($product['category'] ?? ''); ?></div>
                                <div class="product-name"><?php echo htmlspecialchars($product['name']); ?></div>
                                <div class="product-price">$<?php echo number_format($product['price'], 2); ?></div>
                                <button class="btn btn-gradient btn-sm w-100 mt-2 btn-add-to-cart"
                                        data-product-id="<?php echo $product['id']; ?>">
                                    <i class="fa-solid fa-cart-plus"></i> Add
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="text-center mt-5">
            <a href="products.php" class="btn btn-outline-dark px-4">View All Products</a>
        </div>
    </div>
</section>

<section class="py-5 bg-white">
    <div class="container">
        <div class="row align-items-center g-4">
            <div class="col-md-6">
                <img src="images/hero-image1.jpg" class="img-fluid rounded-4" alt="Craftora artisans">
            </div>
            <div class="col-md-6">
                <h2 class="fw-bold mb-3" style="color: var(--clr-primary-dark);">Our Story</h2>
                <p class="text-muted">
                    Craftora brings together independent artisans who pour care and craftsmanship into every piece.
                    From woven textiles to hand-poured soaps, every item tells a story of tradition and creativity.
                </p>
                <a href="about.php" class="btn btn-outline-dark mt-2">Learn More</a>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
