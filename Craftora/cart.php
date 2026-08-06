<?php
require_once 'config.php';

if (!isLoggedIn()) {
    redirect('register.php?redirect=cart.php');
}

$user_id = (int) getUserId();

// Get cart items with product details
$query = "SELECT c.*, p.name, p.description, p.price, p.image, p.stock
          FROM cart c
          JOIN products p ON c.product_id = p.id
          WHERE c.user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$cart_result = $stmt->get_result();

// Calculate totals
$subtotal = 0;
$cart_items = [];
while ($item = $cart_result->fetch_assoc()) {
    $item_total = $item['price'] * $item['quantity'];
    $subtotal += $item_total;
    $cart_items[] = $item;
}

$total = $subtotal;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - Craftora</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container my-5">
        <h1 class="display-5 fw-bold mb-4">Shopping Cart</h1>

        <?php if (count($cart_items) > 0): ?>
        <div class="row">
            <div class="col-lg-8">
                <?php foreach ($cart_items as $item):
                    $item_total = $item['price'] * $item['quantity'];
                ?>
                <div class="cart-item">
                    <div class="row align-items-center">
                        <div class="col-md-2">
                            <img src="images/products/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="img-fluid" onerror="this.src='images/placeholder.jpg'">
                        </div>
                        <div class="col-md-5">
                            <h5><?php echo htmlspecialchars($item['name']); ?></h5>
                            <p class="text-muted mb-1"><?php echo formatPrice($item['price']); ?> each</p>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group">
                                <button class="btn btn-outline-secondary update-quantity" data-id="<?php echo $item['product_id']; ?>" data-action="decrease">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <input type="text" class="form-control text-center quantity-input" value="<?php echo $item['quantity']; ?>" readonly>
                                <button class="btn btn-outline-secondary update-quantity" data-id="<?php echo $item['product_id']; ?>" data-action="increase" data-max="<?php echo $item['stock']; ?>">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                            <small class="text-muted d-block mt-1">Max: <?php echo $item['stock']; ?></small>
                        </div>
                        <div class="col-md-1 text-end">
                            <h5 class="text-primary mb-1"><?php echo formatPrice($item_total); ?></h5>
                        </div>
                        <div class="col-md-1 text-end">
                            <button class="btn btn-link text-danger remove-from-cart" data-id="<?php echo $item['product_id']; ?>">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>

                <div class="mt-3">
                    <a href="products.php" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left"></i> Continue Shopping
                    </a>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="cart-summary">
                    <h4 class="fw-bold mb-4">Order Summary</h4>

                    <div class="d-flex justify-content-between mb-3">
                        <span>Subtotal:</span>
                        <span class="fw-bold"><?php echo formatPrice($subtotal); ?></span>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between mb-4">
                        <span class="h5">Total:</span>
                        <span class="h5 text-primary fw-bold"><?php echo formatPrice($total); ?></span>
                    </div>

                    <a href="checkout.php" class="btn btn-primary w-100 btn-lg">
                        <i class="fas fa-check-circle"></i> Proceed to Checkout
                    </a>

                    <div class="text-center mt-3">
                        <small class="text-muted">
                            <i class="fas fa-lock"></i> Secure Checkout
                        </small>
                    </div>
                </div>
            </div>
        </div>
        <?php else: ?>
        <div class="text-center py-5">
            <i class="fas fa-shopping-cart fa-5x text-muted mb-4"></i>
            <h3 class="mb-3">Your cart is empty</h3>
            <p class="text-muted mb-4">Start shopping now!</p>
            <a href="products.php" class="btn btn-primary btn-lg">
                <i class="fas fa-shopping-bag"></i> Browse Products
            </a>
        </div>
        <?php endif; ?>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/main.js"></script>
</body>
</html>
