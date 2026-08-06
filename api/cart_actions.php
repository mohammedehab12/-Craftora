<?php
require_once '../config.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'not_logged_in']);
    exit();
}

$user_id = (int) getUserId();
$action = $_POST['action'] ?? '';
$product_id = (int) ($_POST['product_id'] ?? 0);

if (!$product_id) {
    echo json_encode(['success' => false, 'message' => 'Invalid product']);
    exit();
}

switch ($action) {
    case 'add':
        // Check if product exists and has stock
        $product_stmt = $conn->prepare("SELECT stock FROM products WHERE id = ?");
        $product_stmt->bind_param('i', $product_id);
        $product_stmt->execute();
        $product_result = $product_stmt->get_result();

        if ($product_result->num_rows === 0) {
            echo json_encode(['success' => false, 'message' => 'Product not found']);
            exit();
        }

        $product = $product_result->fetch_assoc();

        if ($product['stock'] < 1) {
            echo json_encode(['success' => false, 'message' => 'Product out of stock']);
            exit();
        }

        // Check if product already in cart
        $check_stmt = $conn->prepare("SELECT * FROM cart WHERE user_id = ? AND product_id = ?");
        $check_stmt->bind_param('ii', $user_id, $product_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            // Update quantity
            $cart_item = $check_result->fetch_assoc();
            $new_quantity = $cart_item['quantity'] + 1;

            if ($new_quantity > $product['stock']) {
                echo json_encode(['success' => false, 'message' => 'Insufficient stock']);
                exit();
            }

            $update_stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?");
            $update_stmt->bind_param('iii', $new_quantity, $user_id, $product_id);
            $result = $update_stmt->execute();
        } else {
            // Insert new item
            $insert_stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, 1)");
            $insert_stmt->bind_param('ii', $user_id, $product_id);
            $result = $insert_stmt->execute();
        }

        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Product added to cart']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to add product']);
        }
        break;

    case 'update':
        $quantity = (int) ($_POST['quantity'] ?? 1);

        if ($quantity < 1) {
            echo json_encode(['success' => false, 'message' => 'Invalid quantity']);
            exit();
        }

        // Check stock
        $product_stmt = $conn->prepare("SELECT stock FROM products WHERE id = ?");
        $product_stmt->bind_param('i', $product_id);
        $product_stmt->execute();
        $product_result = $product_stmt->get_result();

        if ($product_result->num_rows === 0) {
            echo json_encode(['success' => false, 'message' => 'Product not found']);
            exit();
        }

        $product = $product_result->fetch_assoc();

        if ($quantity > $product['stock']) {
            echo json_encode(['success' => false, 'message' => 'Insufficient stock']);
            exit();
        }

        $update_stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?");
        $update_stmt->bind_param('iii', $quantity, $user_id, $product_id);

        if ($update_stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Quantity updated']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update quantity']);
        }
        break;

    case 'remove':
        $delete_stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ? AND product_id = ?");
        $delete_stmt->bind_param('ii', $user_id, $product_id);

        if ($delete_stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Product removed']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to remove product']);
        }
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}

$conn->close();
?>
