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
                <p class="text-muted mb-1">
                    <i class="fas fa-box-open text-primary"></i>
                    Showing
                    <strong><?php echo $products_result->num_rows; ?></strong>
                    product<?php echo $products_result->num_rows != 1 ? 's' : ''; ?>
                </p>
                <p class="lead text-muted">
                    Explore our handcrafted collection made by talented local artisans.
                </p>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-8">
                <form action="" method="GET" class="row g-3 products-filter">

                    <div class="col-md-5">
                        <input
                            type="search"
                            name="search"
                            class="form-control"
                            placeholder="Search products..."
                            autocomplete="off"
                            value="<?php echo htmlspecialchars($search); ?>">
                    </div>

                    <div class="col-md-3">
                        <select name="category" class="form-select">

                            <option value="">All Categories</option>

                            <?php while ($cat = $categories_result->fetch_assoc()): ?>

                                <option
                                    value="<?php echo htmlspecialchars($cat['category']); ?>"
                                    <?php echo $category == $cat['category'] ? 'selected' : ''; ?>>

                                    <?php echo htmlspecialchars($cat['category']); ?>

                                </option>

                            <?php endwhile; ?>

                        </select>

                    </div>

                    <div class="col-md-2 d-grid">
                        <button class="btn btn-primary">
                            <i class="fas fa-filter"></i> Filter
                        </button>
                    </div>
                    <div class="col-md-2 d-grid">
                        <a href="products.php" class="btn btn-primary px-4">
                            <i class="fas fa-rotate-left"></i>
                            <span>Reset Filters</span>
                        </a>
                    </div>


                </form>
            </div>
        </div>

        <?php if ($products_result->num_rows > 0): ?>
            <div class="row">
                <?php while ($product = $products_result->fetch_assoc()): ?>
                    <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
                        <div class="card product-card h-100 fade-in-up">
                            <img
                                src="images/products/<?php echo htmlspecialchars($product['image']); ?>"
                                class="card-img-top"
                                alt="<?php echo htmlspecialchars($product['name']); ?> Handmade Product"
                                loading="lazy"
                                decoding="async"
                                onerror="this.src='images/placeholder.jpg';">
                            <div class="card-body">
                                <span class="badge bg-secondary mb-2"><?php echo htmlspecialchars($product['category']); ?></span>
                                <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                                <p class="card-text text-muted small"><?php echo htmlspecialchars($product['description']); ?></p>

                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="h5 mb-0 text-primary"><?php echo formatPrice($product['price']); ?></span>
                                    <?php if ($product['stock'] == 0): ?>

                                        <span class="badge bg-danger">
                                            Out of Stock
                                        </span>

                                    <?php elseif ($product['stock'] <= 5): ?>

                                        <span class="badge bg-warning text-dark">
                                            Only <?php echo $product['stock']; ?> Left
                                        </span>

                                    <?php else: ?>

                                        <span class="badge bg-success">
                                            Available
                                        </span>

                                    <?php endif; ?>
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
            <div class="text-center py-5">

                <i class="fas fa-box-open fa-5x text-secondary mb-4"></i>

                <h3>No Matching Products</h3>

                <p class="text-muted">

                    We couldn't find products matching your search.
                    Try another keyword or browse all products.
                </p>

                <a href="products.php"

                    class="btn btn-primary">

                    Browse All Products

                </a>

            </div>
        <?php endif; ?>
    </div>

    <?php include 'includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" defer></script>

    <script src="js/main.js" defer></script>
</body>

</html>