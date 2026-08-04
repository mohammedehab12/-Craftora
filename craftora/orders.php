<?php
require_once 'config.php';

if (!isLoggedIn()) {
    redirect('register.php');
}

$user_id = (int) getUserId();

// Get user orders
$stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY order_date DESC");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$orders_result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - Craftora</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container my-5">
        <h1 class="display-5 fw-bold mb-4">My Orders</h1>

        <?php if ($orders_result->num_rows > 0): ?>
        <div class="row">
            <?php while ($order = $orders_result->fetch_assoc()):
                // Get order items
                $order_id = $order['id'];
                $items_stmt = $conn->prepare(
                    "SELECT oi.*, p.name, p.image
                     FROM order_items oi
                     JOIN products p ON oi.product_id = p.id
                     WHERE oi.order_id = ?"
                );
                $items_stmt->bind_param('i', $order_id);
                $items_stmt->execute();
                $items_result = $items_stmt->get_result();

                $status_colors = [
                    'pending' => 'warning',
                    'processing' => 'info',
                    'shipped' => 'primary',
                    'delivered' => 'success',
                    'cancelled' => 'danger'
                ];
                $status_color = $status_colors[$order['status']] ?? 'secondary';
            ?>
            <div class="col-md-12 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <div class="row align-items-center">
                            <div class="col-md-3">
                                <small class="text-muted">Order ID</small>
                                <p class="mb-0 fw-bold">#<?php echo str_pad($order['id'], 6, '0', STR_PAD_LEFT); ?></p>
                            </div>
                            <div class="col-md-3">
                                <small class="text-muted">Date</small>
                                <p class="mb-0"><?php echo date('d M Y', strtotime($order['order_date'])); ?></p>
                            </div>
                            <div class="col-md-3">
                                <small class="text-muted">Total</small>
                                <p class="mb-0 fw-bold text-primary"><?php echo formatPrice($order['total_price']); ?></p>
                            </div>
                            <div class="col-md-3 text-end">
                                <span class="badge bg-<?php echo $status_color; ?> p-2">
                                    <?php echo ucfirst($order['status']); ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <h6 class="fw-bold mb-3">Order Items</h6>

                        <?php while ($item = $items_result->fetch_assoc()): ?>
                        <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                            <img src="images/products/<?php echo $item['image']; ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;" onerror="this.src='images/placeholder.jpg'">
                            <div class="ms-3 flex-grow-1">
                                <h6 class="mb-1"><?php echo htmlspecialchars($item['name']); ?></h6>
                                <small class="text-muted">Quantity: <?php echo $item['quantity']; ?> × <?php echo formatPrice($item['price']); ?></small>
                            </div>
                            <div class="text-end">
                                <p class="mb-0 fw-bold"><?php echo formatPrice($item['price'] * $item['quantity']); ?></p>
                            </div>
                        </div>
                        <?php endwhile; ?>

                        <div class="row mt-3">
                            <div class="col-md-12">
                                <p class="mb-1"><i class="fas fa-credit-card text-muted"></i> Payment: <strong><?php echo htmlspecialchars($order['payment_method']); ?></strong></p>
                                <p class="mb-0"><i class="fas fa-map-marker-alt text-muted"></i> Address: <?php echo nl2br(htmlspecialchars($order['shipping_address'])); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
        <?php else: ?>
        <div class="text-center py-5">
            <i class="fas fa-shopping-bag fa-5x text-muted mb-4"></i>
            <h3 class="mb-3">No orders yet</h3>
            <p class="text-muted mb-4">Start shopping today!</p>
            <a href="products.php" class="btn btn-primary btn-lg">
                <i class="fas fa-shopping-cart"></i> Browse Products
            </a>
        </div>
        <?php endif; ?>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
