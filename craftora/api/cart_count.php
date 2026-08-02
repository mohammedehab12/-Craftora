<?php
/**
 * api/cart_count.php
 * Returns the number of items (sum of quantities) in the current user's cart as JSON.
 */

require_once __DIR__ . '/../config.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['count' => 0]);
    exit;
}

try {
    $stmt = $pdo->prepare('SELECT COALESCE(SUM(quantity), 0) AS total FROM cart WHERE user_id = :uid');
    $stmt->execute(['uid' => currentUserId()]);
    $total = (int) $stmt->fetchColumn();

    echo json_encode(['count' => $total]);
} catch (PDOException $e) {
    echo json_encode(['count' => 0]);
}
