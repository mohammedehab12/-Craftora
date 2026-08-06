<?php
require_once __DIR__ . '/config.php';

$category = trim($_GET['category'] ?? '');
$search   = trim($_GET['search'] ?? '');

$sql    = 'SELECT * FROM products WHERE 1=1';
$params = [];

if ($category !== '') {
    $sql .= ' AND category = :category';
    $params['category'] = $category;
}
if ($search !== '') {
    $sql .= ' AND name LIKE :search';
    $params['search'] = '%' . $search . '%';
}
$sql .= ' ORDER BY created_at DESC';

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

$catStmt = $pdo->query('SELECT DISTINCT category FROM products WHERE category IS NOT NULL ORDER BY category');
$categories = $catStmt->fetchAll(PDO::FETCH_COLUMN);

$pageTitle  = 'Products - Craftora';
$activePage = 'products';
include __DIR__ . '/includes/header.php';
?>

<section class="py-5">
    <div class="container">
        <h1 class="section-title">Our Products</h1>

        <form method="GET" action="products.php" class="row g-2 justify-content-center mb-5">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Search products..."
                       value="<?php echo htmlspecialchars($search); ?>">
            </div>
            <div class="col-md-3">
                <select name="category" class="form-select" onchange="this.form.submit()">
                    <option value="">All Categories</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo htmlspecialchars($cat); ?>" <?php echo $category === $cat ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-dark w-100">Filter</button>
            </div>
        </form>

        <?php if (empty($products)): ?>
            <div class="empty-state">
                <i class="fa-solid fa-magnifying-glass"></i>
                <p>No products found. Try a different search or category.</p>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($products as $product): ?>
                    <div class="col-6 col-md-4 col-lg-3">
                        <div class="product-card position-relative">
                            <?php if ($product['featured']): ?>
                                <span class="badge badge-featured position-absolute m-2">Featured</span>
                            <?php endif; ?>
                            <img src="<?php echo htmlspecialchars($product['image'] ?: 'images/placeholder.jpg'); ?>"
                                 alt="<?php echo htmlspecialchars($product['name']); ?>" class="product-img">
                            <div class="product-body">
                                <div class="product-category"><?php echo htmlspecialchars($product['category'] ?? ''); ?></div>
                                <div class="product-name"><?php echo htmlspecialchars($product['name']); ?></div>
                                <p class="text-muted small mb-2">
                                    <?php echo htmlspecialchars(mb_strimwidth($product['description'] ?? '', 0, 70, '...')); ?>
                                </p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="product-price">$<?php echo number_format($product['price'], 2); ?></span>
                                    <?php if ($product['stock'] < 1): ?>
                                        <span class="badge bg-secondary">Out of stock</span>
                                    <?php elseif ($product['stock'] <= 5): ?>
                                        <span class="badge badge-stock-low">Only <?php echo $product['stock']; ?> left</span>
                                    <?php endif; ?>
                                </div>
                                <button class="btn btn-gradient btn-sm w-100 mt-2 btn-add-to-cart"
                                        data-product-id="<?php echo $product['id']; ?>"
                                        <?php echo $product['stock'] < 1 ? 'disabled' : ''; ?>>
                                    <i class="fa-solid fa-cart-plus"></i> Add to Cart
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
