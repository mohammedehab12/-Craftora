<?php
require_once __DIR__ . '/../includes/admin_auth.php';

$errors  = [];
$success = '';

// ---- Handle delete ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    $id = (int) ($_POST['id'] ?? 0);
    $stmt = $pdo->prepare('DELETE FROM products WHERE id = :id');
    $stmt->execute(['id' => $id]);
    header('Location: products.php?deleted=1');
    exit;
}

// ---- Handle add / edit ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && in_array($_POST['action'] ?? '', ['add', 'edit'])) {
    $name        = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price       = (float) ($_POST['price'] ?? 0);
    $category    = trim($_POST['category'] ?? '');
    $image       = trim($_POST['image'] ?? '');
    $stock       = (int) ($_POST['stock'] ?? 0);
    $featured    = isset($_POST['featured']) ? 1 : 0;
    $productId   = (int) ($_POST['id'] ?? 0);

    if ($name === '')      $errors[] = 'اسم المنتج مطلوب.';
    if ($price <= 0)       $errors[] = 'السعر يجب أن يكون أكبر من صفر.';
    if ($category === '')  $errors[] = 'التصنيف مطلوب.';
    if ($image === '')     $errors[] = 'اسم ملف الصورة مطلوب (مثال: wallet.jpg).';
    if ($stock < 0)        $errors[] = 'الكمية لا يمكن أن تكون سالبة.';

    if (empty($errors)) {
        if ($_POST['action'] === 'add') {
            $stmt = $pdo->prepare('
                INSERT INTO products (name, description, price, category, image, stock, featured)
                VALUES (:name, :description, :price, :category, :image, :stock, :featured)
            ');
            $stmt->execute([
                'name' => $name, 'description' => $description, 'price' => $price,
                'category' => $category, 'image' => $image, 'stock' => $stock, 'featured' => $featured,
            ]);
            header('Location: products.php?added=1');
            exit;
        } else {
            $stmt = $pdo->prepare('
                UPDATE products SET name = :name, description = :description, price = :price,
                       category = :category, image = :image, stock = :stock, featured = :featured
                WHERE id = :id
            ');
            $stmt->execute([
                'name' => $name, 'description' => $description, 'price' => $price,
                'category' => $category, 'image' => $image, 'stock' => $stock,
                'featured' => $featured, 'id' => $productId,
            ]);
            header('Location: products.php?updated=1');
            exit;
        }
    }
}

$editProduct = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare('SELECT * FROM products WHERE id = :id');
    $stmt->execute(['id' => (int) $_GET['edit']]);
    $editProduct = $stmt->fetch();
}

$products = $pdo->query('SELECT * FROM products ORDER BY created_at DESC')->fetchAll();

$pageTitle       = 'Products - Craftora Admin';
$activeAdminPage = 'products';
include __DIR__ . '/../includes/admin_header.php';
?>

<?php if (isset($_GET['added'])): ?>
    <div class="alert alert-success">Product added successfully.</div>
<?php elseif (isset($_GET['updated'])): ?>
    <div class="alert alert-success">Product updated successfully.</div>
<?php elseif (isset($_GET['deleted'])): ?>
    <div class="alert alert-success">Product deleted.</div>
<?php endif; ?>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <ul class="mb-0 ps-3">
            <?php foreach ($errors as $err): ?><li><?php echo htmlspecialchars($err); ?></li><?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="admin-card">
            <h6 class="fw-bold mb-3"><?php echo $editProduct ? 'Edit Product' : 'Add New Product'; ?></h6>
            <form method="POST" action="products.php<?php echo $editProduct ? '?edit=' . $editProduct['id'] : ''; ?>">
                <input type="hidden" name="action" value="<?php echo $editProduct ? 'edit' : 'add'; ?>">
                <?php if ($editProduct): ?>
                    <input type="hidden" name="id" value="<?php echo $editProduct['id']; ?>">
                <?php endif; ?>

                <div class="mb-2">
                    <label class="form-label small">Name</label>
                    <input type="text" name="name" class="form-control form-control-sm"
                           value="<?php echo htmlspecialchars($editProduct['name'] ?? ''); ?>" required>
                </div>
                <div class="mb-2">
                    <label class="form-label small">Description</label>
                    <textarea name="description" class="form-control form-control-sm" rows="2"><?php
                        echo htmlspecialchars($editProduct['description'] ?? '');
                    ?></textarea>
                </div>
                <div class="row g-2 mb-2">
                    <div class="col-6">
                        <label class="form-label small">Price ($)</label>
                        <input type="number" step="0.01" name="price" class="form-control form-control-sm"
                               value="<?php echo htmlspecialchars($editProduct['price'] ?? ''); ?>" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label small">Stock</label>
                        <input type="number" name="stock" class="form-control form-control-sm"
                               value="<?php echo htmlspecialchars($editProduct['stock'] ?? 0); ?>" required>
                    </div>
                </div>
                <div class="mb-2">
                    <label class="form-label small">Category</label>
                    <input type="text" name="category" class="form-control form-control-sm"
                           value="<?php echo htmlspecialchars($editProduct['category'] ?? ''); ?>" required>
                </div>
                <div class="mb-2">
                    <label class="form-label small">Image filename</label>
                    <input type="text" name="image" class="form-control form-control-sm" placeholder="e.g. wallet.jpg"
                           value="<?php echo htmlspecialchars($editProduct['image'] ?? ''); ?>" required>
                    <div class="form-text">File must exist in <code>images/products/</code>.</div>
                </div>
                <div class="form-check mb-3">
                    <input type="checkbox" class="form-check-input" name="featured" id="featured"
                           <?php echo !empty($editProduct['featured']) ? 'checked' : ''; ?>>
                    <label class="form-check-label small" for="featured">Featured on homepage</label>
                </div>

                <button type="submit" class="btn btn-admin w-100 btn-sm">
                    <?php echo $editProduct ? 'Save Changes' : 'Add Product'; ?>
                </button>
                <?php if ($editProduct): ?>
                    <a href="products.php" class="btn btn-outline-secondary btn-sm w-100 mt-2">Cancel</a>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="admin-card">
            <h6 class="fw-bold mb-3">All Products (<?php echo count($products); ?>)</h6>
            <div class="table-responsive">
                <table class="table admin-table mb-0">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $p): ?>
                            <tr>
                                <td><img src="<?php echo htmlspecialchars(productImageUrl($p['image'])); ?>" class="thumb"></td>
                                <td>
                                    <?php echo htmlspecialchars($p['name']); ?>
                                    <?php if ($p['featured']): ?><span class="badge bg-warning text-dark ms-1">★</span><?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($p['category']); ?></td>
                                <td>$<?php echo number_format($p['price'], 2); ?></td>
                                <td>
                                    <?php echo (int) $p['stock']; ?>
                                    <?php if ($p['stock'] <= 5): ?><span class="badge bg-danger">Low</span><?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <a href="products.php?edit=<?php echo $p['id']; ?>" class="btn btn-sm btn-outline-secondary">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                    <form method="POST" action="products.php" class="d-inline"
                                          onsubmit="return confirm('Delete this product?');">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?php echo $p['id']; ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/admin_footer.php'; ?>
