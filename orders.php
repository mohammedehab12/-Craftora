<?php
require_once __DIR__ . '/config.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$stmt = $pdo->prepare('SELECT * FROM orders WHERE user_id = :uid ORDER BY order_date DESC');
$stmt->execute(['uid' => currentUserId()]);
$orders = $stmt->fetchAll();

$itemsStmt = $pdo->prepare('
    SELECT oi.quantity, oi.price, p.name, p.image
    FROM order_items oi
    JOIN products p ON p.id = oi.product_id
    WHERE oi.order_id = :oid
');

$pageTitle  = 'My Orders - Craftora';
$activePage = 'orders';
include __DIR__ . '/includes/header.php';
?>

<section class="py-5">
    <div class="container">
        <h1 class="section-title">My Orders</h1>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success text-center">
                <i class="fa-solid fa-circle-check me-2"></i>Your order has been placed successfully!
            </div>
        <?php endif; ?>

        <?php if (empty($orders)): ?>
            <div class="empty-state">
                <i class="fa-solid fa-box"></i>
                <p>You haven't placed any orders yet.</p>
                <a href="products.php" class="btn btn-gradient px-4">Start Shopping</a>
            </div>
        <?php else: ?>
            <?php foreach ($orders as $order): ?>
                <?php
                    $itemsStmt->execute(['oid' => $order['id']]);
                    $items = $itemsStmt->fetchAll();

                    $statusClass = match ($order['status']) {
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        'shipped'   => 'info',
                        default     => 'warning',
                    };
                ?>
                <div class="summary-box mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                        <div>
                            <div class="fw-bold">Order #<?php echo $order['id']; ?></div>
                            <div class="text-muted small"><?php echo date('M d, Y - H:i', strtotime($order['order_date'])); ?></div>
                        </div>
                        <span class="badge bg-<?php echo $statusClass; ?> text-capitalize">
                            <?php echo htmlspecialchars($order['status']); ?>
                        </span>
                    </div>

                    <?php foreach ($items as $item): ?>
                        <div class="d-flex justify-content-between small mb-2">
                            <span><?php echo htmlspecialchars($item['name']); ?> &times; <?php echo $item['quantity']; ?></span>
                            <span>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                        </div>
                    <?php endforeach; ?>

                    <hr>
                    <div class="d-flex justify-content-between small text-muted mb-1">
                        <span>Shipping to</span>
                        <span><?php echo htmlspecialchars($order['shipping_address']); ?></span>
                    </div>
                    <div class="d-flex justify-content-between small text-muted mb-2">
                        <span>Payment</span>
                        <span class="text-capitalize"><?php echo str_replace('_', ' ', htmlspecialchars($order['payment_method'])); ?></span>
                    </div>
                    <div class="d-flex justify-content-between fw-bold">
                        <span>Total</span>
                        <span>$<?php echo number_format($order['total_price'], 2); ?></span>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
