<?php
require_once 'config.php';

if (!isLoggedIn()) {
    redirect('register.php?redirect=checkout.php');
}

$user_id = (int) getUserId();

// Get cart items
$query = "SELECT c.*, p.name, p.price, p.stock
          FROM cart c
          JOIN products p ON c.product_id = p.id
          WHERE c.user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$cart_result = $stmt->get_result();

if ($cart_result->num_rows === 0) {
    redirect('cart.php');
}

// Calculate totals
$subtotal = 0;
$cart_items = [];

while ($item = $cart_result->fetch_assoc()) {
    $item_total = $item['price'] * $item['quantity'];
    $subtotal += $item_total;
    $cart_items[] = $item;
}

$total = $subtotal;

// Get user info
$user_stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$user_stmt->bind_param('i', $user_id);
$user_stmt->execute();
$user = $user_stmt->get_result()->fetch_assoc();

if (!$user) {
    session_unset();
    session_destroy();
    session_start();
    redirect('login.php?error=account_not_found');
}

$error = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    $address = sanitize($_POST['address'] ?? '');
    $payment_method = sanitize($_POST['payment_method'] ?? '');

    $allowed_payment_methods = ['Cash on Delivery', 'Credit Card', 'Mobile Wallet'];

    if (empty($name) || empty($phone) || empty($address) || empty($payment_method)) {
        $error = 'Please fill in all fields';
    } elseif (!in_array($payment_method, $allowed_payment_methods, true)) {
        $error = 'Invalid payment method';
    } else {
        // Start transaction
        $conn->begin_transaction();

        try {
            // Re-check stock for every item before placing the order
            foreach ($cart_items as $item) {
                $stock_stmt = $conn->prepare("SELECT stock FROM products WHERE id = ? FOR UPDATE");
                $stock_stmt->bind_param('i', $item['product_id']);
                $stock_stmt->execute();
                $current_stock = $stock_stmt->get_result()->fetch_assoc();

                if (!$current_stock || $current_stock['stock'] < $item['quantity']) {
                    throw new Exception('"' . $item['name'] . '" is no longer available in the requested quantity');
                }
            }

            // Create order
            $order_stmt = $conn->prepare(
                "INSERT INTO orders (user_id, total_price, payment_method, shipping_address)
                 VALUES (?, ?, ?, ?)"
            );
            $order_stmt->bind_param('idss', $user_id, $total, $payment_method, $address);

            if (!$order_stmt->execute()) {
                throw new Exception('Failed to create order');
            }

            $order_id = $conn->insert_id;

            // Insert order items and update stock
            $item_stmt = $conn->prepare(
                "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)"
            );
            $stock_update_stmt = $conn->prepare(
                "UPDATE products SET stock = stock - ? WHERE id = ?"
            );

            foreach ($cart_items as $item) {
                $product_id = $item['product_id'];
                $quantity = $item['quantity'];
                $price = $item['price'];

                $item_stmt->bind_param('iiid', $order_id, $product_id, $quantity, $price);
                if (!$item_stmt->execute()) {
                    throw new Exception('Failed to add order items');
                }

                $stock_update_stmt->bind_param('ii', $quantity, $product_id);
                $stock_update_stmt->execute();
            }

            // Clear cart
            $clear_stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
            $clear_stmt->bind_param('i', $user_id);
            $clear_stmt->execute();

            // Commit transaction
            $conn->commit();
            $success = true;

        } catch (Exception $e) {
            $conn->rollback();
            $error = 'Order failed: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Craftora</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container my-5">
        <?php if ($success): ?>
        <div class="row justify-content-center">
            <div class="col-md-8 text-center">
                <div class="card shadow-lg border-0 p-5" style="border-radius: 15px;">
                    <i class="fas fa-check-circle text-success fa-5x mb-4"></i>
                    <h1 class="display-5 fw-bold mb-3">Thank You for Your Order! 🎉</h1>
                    <p class="lead text-muted mb-4">Your order has been placed successfully.</p>

                    <div class="d-flex gap-3 justify-content-center">
                        <a href="orders.php" class="btn btn-primary btn-lg">
                            <i class="fas fa-shopping-bag"></i> View Orders
                        </a>
                        <a href="products.php" class="btn btn-outline-primary btn-lg">
                            <i class="fas fa-shopping-cart"></i> Continue Shopping
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <?php else: ?>
        <h1 class="display-5 fw-bold mb-4">Checkout</h1>

        <?php if ($error): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
        </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h4 class="fw-bold mb-4"><i class="fas fa-shipping-fast"></i> Shipping Information</h4>

                        <form method="POST" action="">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="name" class="form-label">Full Name *</label>
                                    <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="phone" class="form-label">Phone Number *</label>
                                    <input type="tel" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="address" class="form-label">Shipping Address *</label>
                                <textarea class="form-control" id="address" name="address" rows="3" required><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                            </div>

                            <h5 class="fw-bold mt-4 mb-3"><i class="fas fa-credit-card"></i> Payment Method</h5>

                            <div class="form-check mb-3">
                                <input class="form-check-input" type="radio" name="payment_method" id="cod" value="Cash on Delivery" checked>
                                <label class="form-check-label" for="cod">
                                    <i class="fas fa-money-bill-wave"></i> Cash on Delivery
                                </label>
                            </div>

                            <div class="form-check mb-3">
                                <input class="form-check-input" type="radio" name="payment_method" id="card" value="Credit Card">
                                <label class="form-check-label" for="card">
                                    <i class="fas fa-credit-card"></i> Credit/Debit Card (Demo)
                                </label>
                            </div>

                            <div class="form-check mb-4">
                                <input class="form-check-input" type="radio" name="payment_method" id="mobile" value="Mobile Wallet">
                                <label class="form-check-label" for="mobile">
                                    <i class="fas fa-mobile-alt"></i> Mobile Wallet (Demo)
                                </label>
                            </div>

                            <button type="submit" class="btn btn-primary btn-lg w-100">
                                <i class="fas fa-check-circle"></i> Place Order
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="cart-summary">
                    <h4 class="fw-bold mb-4">Order Summary</h4>

                    <?php foreach ($cart_items as $item): ?>
                    <div class="d-flex justify-content-between mb-2">
                        <span><?php echo htmlspecialchars($item['name']); ?> × <?php echo $item['quantity']; ?></span>
                        <span><?php echo formatPrice($item['price'] * $item['quantity']); ?></span>
                    </div>
                    <?php endforeach; ?>

                    <hr>

                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal:</span>
                        <span class="fw-bold"><?php echo formatPrice($subtotal); ?></span>
                    </div>

                    <div class="d-flex justify-content-between mb-4">
                        <span class="h5">Total:</span>
                        <span class="h5 text-primary fw-bold"><?php echo formatPrice($total); ?></span>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
