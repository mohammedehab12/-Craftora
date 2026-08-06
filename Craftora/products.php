<?php
require_once 'config.php';

// Get all products with optional category filter
$category = isset($_GET['category']) ? sanitize($_GET['category']) : '';
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';

$query = "SELECT * FROM products WHERE 1=1";
$types = '';
$params = [];

if ($category !== '') {
    $query .= " AND category = ?";
    $types .= 's';
    $params[] = $category;
}

if ($search !== '') {
    $query .= " AND (name LIKE CONCAT('%', ?, '%') OR description LIKE CONCAT('%', ?, '%'))";
    $types .= 'ss';
    $params[] = $search;
    $params[] = $search;
}

$query .= " ORDER BY created_at DESC";

$stmt = $conn->prepare($query);
if ($types !== '') {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$products_result = $stmt->get_result();

// Get categories
$categories_query = "SELECT DISTINCT category FROM products ORDER BY category";
$categories_result = $conn->query($categories_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop - Craftora</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container my-5">
        <div class="row mb-4">
            <div class="col-md-12">
                <h1 class="display-4 fw-bold mb-3">Our Handmade Products</h1>
                <p class="lead text-muted">Unique crafts made by skilled local artisans</p>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-8">
                <form action="" method="GET" class="d-flex">
                    <input type="text" name="search" class="form-control me-2" placeholder="Search products..." value="<?php echo htmlspecialchars($search); ?>">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Search
                    </button>
                </form>
            </div>
            <div class="col-md-4">
                <select class="form-select" onchange="window.location.href='products.php?category='+encodeURIComponent(this.value)">
                    <option value="">All Categories</option>
                    <?php while($cat = $categories_result->fetch_assoc()): ?>
                        <option value="<?php echo htmlspecialchars($cat['category']); ?>" <?php echo $category === $cat['category'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat['category']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
        </div>

        <?php if ($products_result->num_rows > 0): ?>
        <div class="row">
            <?php while($product = $products_result->fetch_assoc()): ?>
            <div class="col-md-4 mb-4">
                <div class="card product-card h-100">
                    <img src="images/products/<?php echo $product['image']; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($product['name']); ?>" onerror="this.src='images/placeholder.jpg'">
                    <div class="card-body">
                        <span class="badge bg-secondary mb-2"><?php echo htmlspecialchars($product['category']); ?></span>
                        <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                        <p class="card-text text-muted"><?php echo htmlspecialchars($product['description']); ?></p>

                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="h5 mb-0 text-primary"><?php echo formatPrice($product['price']); ?></span>
                            <small class="text-muted">
                                <i class="fas fa-box"></i> Stock: <?php echo $product['stock']; ?>
                            </small>
                        </div>

                        <?php if ($product['stock'] > 0): ?>
                            <button class="btn btn-primary w-100 add-to-cart" data-id="<?php echo $product['id']; ?>">
                                <i class="fas fa-shopping-cart"></i> Add to Cart
                            </button>
                        <?php else: ?>
                            <button class="btn btn-secondary w-100" disabled>
                                Out of Stock
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
        <?php else: ?>
        <div class="alert alert-info text-center">
            <i class="fas fa-info-circle fa-2x mb-3"></i>
            <p class="mb-0">No products found. Try a different search or category.</p>
        </div>
        <?php endif; ?>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/main.js"></script>
</body>
</html>
