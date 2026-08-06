<?php
require_once __DIR__ . '/config.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$stmt = $pdo->prepare('
    SELECT c.id AS cart_id, c.quantity, p.id AS product_id, p.name, p.price, p.image, p.stock
    FROM cart c
    JOIN products p ON p.id = c.product_id
    WHERE c.user_id = :uid
    ORDER BY c.added_at DESC
');
$stmt->execute(['uid' => currentUserId()]);
$cartItems = $stmt->fetchAll();

$cartTotal = 0;
foreach ($cartItems as $item) {
    $cartTotal += $item['price'] * $item['quantity'];
}

$pageTitle  = 'Your Cart - Craftora';
$activePage = 'cart';
include __DIR__ . '/includes/header.php';
?>

<section class="py-5">
    <div class="container">
        <h1 class="section-title">Your Cart</h1>

        <?php if (empty($cartItems)): ?>
            <div class="empty-state">
                <i class="fa-solid fa-cart-shopping"></i>
                <p>Your cart is empty.</p>
                <a href="products.php" class="btn btn-gradient px-4">Start Shopping</a>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <div class="col-lg-8">
                    <?php foreach ($cartItems as $item): ?>
                        <div class="d-flex align-items-center gap-3 mb-3 p-3 bg-white rounded-3 cart-item-row"
                             data-cart-id="<?php echo $item['cart_id']; ?>">
                            <img src="<?php echo htmlspecialchars($item['image'] ?: 'images/placeholder.jpg'); ?>"
                                 alt="<?php echo htmlspecialchars($item['name']); ?>">

                            <div class="flex-grow-1">
                                <div class="fw-semibold"><?php echo htmlspecialchars($item['name']); ?></div>
                                <div class="text-muted small">$<?php echo number_format($item['price'], 2); ?> each</div>
                            </div>

                            <div class="d-flex align-items-center gap-2">
                                <button type="button" class="btn btn-sm btn-outline-secondary qty-btn" data-action="decrease">-</button>
                                <input type="number" class="form-control form-control-sm text-center qty-input"
                                       style="width: 55px;" value="<?php echo $item['quantity']; ?>" min="1">
                                <button type="button" class="btn btn-sm btn-outline-secondary qty-btn" data-action="increase">+</button>
                            </div>

                            <div class="fw-bold" style="width: 90px; text-align: right;">
                                $<span class="line-total"><?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                            </div>

                            <button type="button" class="btn btn-sm btn-outline-danger btn-remove-item">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="col-lg-4">
                    <div class="summary-box">
                        <h5 class="fw-bold mb-3">Order Summary</h5>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Subtotal</span>
                            <span>$<span id="cartTotal"><?php echo number_format($cartTotal, 2); ?></span></span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">Shipping</span>
                            <span>Calculated at checkout</span>
                        </div>
                        <hr>
                        <a href="checkout.php" class="btn btn-gradient w-100">Proceed to Checkout</a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
