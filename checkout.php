<?php
require_once __DIR__ . '/config.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$userId = currentUserId();

$stmt = $pdo->prepare('
    SELECT c.id AS cart_id, c.quantity, p.id AS product_id, p.name, p.price, p.stock
    FROM cart c
    JOIN products p ON p.id = c.product_id
    WHERE c.user_id = :uid
');
$stmt->execute(['uid' => $userId]);
$cartItems = $stmt->fetchAll();

if (empty($cartItems)) {
    header('Location: cart.php');
    exit;
}

$cartTotal = 0;
foreach ($cartItems as $item) {
    $cartTotal += $item['price'] * $item['quantity'];
}

$userStmt = $pdo->prepare('SELECT name, email, address FROM users WHERE id = :id');
$userStmt->execute(['id' => $userId]);
$user = $userStmt->fetch();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $shippingAddress = trim($_POST['shipping_address'] ?? '');
    $paymentMethod   = trim($_POST['payment_method'] ?? 'cash_on_delivery');

    if ($shippingAddress === '') {
        $errors[] = 'العنوان مطلوب لإتمام الطلب.';
    }

    if (empty($errors)) {
        try {
            $pdo->beginTransaction();

            $orderStmt = $pdo->prepare('
                INSERT INTO orders (user_id, total_price, status, payment_method, shipping_address)
                VALUES (:uid, :total, "pending", :method, :address)
            ');
            $orderStmt->execute([
                'uid'     => $userId,
                'total'   => $cartTotal,
                'method'  => $paymentMethod,
                'address' => $shippingAddress,
            ]);
            $orderId = $pdo->lastInsertId();

            $itemStmt  = $pdo->prepare('INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (:oid, :pid, :qty, :price)');
            $stockStmt = $pdo->prepare('UPDATE products SET stock = stock - :qty WHERE id = :pid AND stock >= :qty2');

            foreach ($cartItems as $item) {
                $itemStmt->execute([
                    'oid'   => $orderId,
                    'pid'   => $item['product_id'],
                    'qty'   => $item['quantity'],
                    'price' => $item['price'],
                ]);
                $stockStmt->execute([
                    'qty'  => $item['quantity'],
                    'pid'  => $item['product_id'],
                    'qty2' => $item['quantity'],
                ]);
            }

            $clearStmt = $pdo->prepare('DELETE FROM cart WHERE user_id = :uid');
            $clearStmt->execute(['uid' => $userId]);

            $pdo->commit();

            header('Location: orders.php?success=1');
            exit;
        } catch (PDOException $e) {
            $pdo->rollBack();
            $errors[] = 'حدث خطأ أثناء تنفيذ الطلب. حاول مرة أخرى.';
        }
    }
}

$pageTitle  = 'Checkout - Craftora';
$activePage = 'cart';
include __DIR__ . '/includes/header.php';
?>

<section class="py-5">
    <div class="container">
        <h1 class="section-title">Checkout</h1>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul class="mb-0 ps-3">
                    <?php foreach ($errors as $err): ?>
                        <li><?php echo htmlspecialchars($err); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="row g-4">
            <div class="col-lg-7">
                <div class="summary-box">
                    <h5 class="fw-bold mb-3">Shipping & Payment</h5>
                    <form method="POST" action="checkout.php">
                        <div class="mb-3">
                            <label class="form-label">Shipping Address</label>
                            <textarea name="shipping_address" class="form-control" rows="3" required><?php
                                echo htmlspecialchars($user['address'] ?? '');
                            ?></textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label d-block">Payment Method</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" value="cash_on_delivery" id="cod" checked>
                                <label class="form-check-label" for="cod">Cash on Delivery</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" value="credit_card" id="cc">
                                <label class="form-check-label" for="cc">Credit / Debit Card</label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-gradient w-100">Place Order</button>
                    </form>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="summary-box">
                    <h5 class="fw-bold mb-3">Order Summary</h5>
                    <?php foreach ($cartItems as $item): ?>
                        <div class="d-flex justify-content-between small mb-2">
                            <span><?php echo htmlspecialchars($item['name']); ?> &times; <?php echo $item['quantity']; ?></span>
                            <span>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                        </div>
                    <?php endforeach; ?>
                    <hr>
                    <div class="d-flex justify-content-between fw-bold">
                        <span>Total</span>
                        <span>$<?php echo number_format($cartTotal, 2); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
