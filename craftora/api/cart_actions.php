<?php
/**
 * api/cart_actions.php
 * Handles AJAX cart operations: add, update, remove.
 * Expects POST requests. Always responds with JSON.
 */

require_once __DIR__ . '/../config.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Please log in to manage your cart.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit;
}

$userId = currentUserId();
$action = $_POST['action'] ?? '';

try {
    switch ($action) {

        case 'add':
            $productId = (int) ($_POST['product_id'] ?? 0);
            $quantity  = max(1, (int) ($_POST['quantity'] ?? 1));

            if ($productId <= 0) {
                echo json_encode(['success' => false, 'message' => 'Invalid product.']);
                exit;
            }

            // Confirm product exists and has stock
            $stmt = $pdo->prepare('SELECT stock FROM products WHERE id = :pid');
            $stmt->execute(['pid' => $productId]);
            $product = $stmt->fetch();

            if (!$product) {
                echo json_encode(['success' => false, 'message' => 'Product not found.']);
                exit;
            }
            if ($product['stock'] < 1) {
                echo json_encode(['success' => false, 'message' => 'This product is out of stock.']);
                exit;
            }

            // Upsert cart item
            $stmt = $pdo->prepare('SELECT id, quantity FROM cart WHERE user_id = :uid AND product_id = :pid');
            $stmt->execute(['uid' => $userId, 'pid' => $productId]);
            $existing = $stmt->fetch();

            if ($existing) {
                $newQty = $existing['quantity'] + $quantity;
                $stmt = $pdo->prepare('UPDATE cart SET quantity = :qty WHERE id = :cid');
                $stmt->execute(['qty' => $newQty, 'cid' => $existing['id']]);
            } else {
                $stmt = $pdo->prepare('INSERT INTO cart (user_id, product_id, quantity) VALUES (:uid, :pid, :qty)');
                $stmt->execute(['uid' => $userId, 'pid' => $productId, 'qty' => $quantity]);
            }

            echo json_encode(['success' => true]);
            break;

        case 'update':
            $cartId   = (int) ($_POST['cart_id'] ?? 0);
            $quantity = max(1, (int) ($_POST['quantity'] ?? 1));

            $stmt = $pdo->prepare('
                SELECT c.id, p.price
                FROM cart c
                JOIN products p ON p.id = c.product_id
                WHERE c.id = :cid AND c.user_id = :uid
            ');
            $stmt->execute(['cid' => $cartId, 'uid' => $userId]);
            $item = $stmt->fetch();

            if (!$item) {
                echo json_encode(['success' => false, 'message' => 'Cart item not found.']);
                exit;
            }

            $stmt = $pdo->prepare('UPDATE cart SET quantity = :qty WHERE id = :cid');
            $stmt->execute(['qty' => $quantity, 'cid' => $cartId]);

            $lineTotal = $quantity * $item['price'];

            $stmt = $pdo->prepare('
                SELECT COALESCE(SUM(c.quantity * p.price), 0) AS total
                FROM cart c JOIN products p ON p.id = c.product_id
                WHERE c.user_id = :uid
            ');
            $stmt->execute(['uid' => $userId]);
            $cartTotal = (float) $stmt->fetchColumn();

            echo json_encode([
                'success'     => true,
                'line_total'  => number_format($lineTotal, 2),
                'cart_total'  => number_format($cartTotal, 2),
            ]);
            break;

        case 'remove':
            $cartId = (int) ($_POST['cart_id'] ?? 0);

            $stmt = $pdo->prepare('DELETE FROM cart WHERE id = :cid AND user_id = :uid');
            $stmt->execute(['cid' => $cartId, 'uid' => $userId]);

            $stmt = $pdo->prepare('
                SELECT COALESCE(SUM(c.quantity * p.price), 0) AS total, COUNT(*) AS item_count
                FROM cart c JOIN products p ON p.id = c.product_id
                WHERE c.user_id = :uid
            ');
            $stmt->execute(['uid' => $userId]);
            $row = $stmt->fetch();

            echo json_encode([
                'success'    => true,
                'cart_total' => number_format((float) $row['total'], 2),
                'empty'      => ((int) $row['item_count']) === 0,
            ]);
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Unknown action.']);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error. Please try again.']);
}
