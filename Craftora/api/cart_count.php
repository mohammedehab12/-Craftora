<?php
require_once '../config.php';

header('Content-Type: application/json');

$count = 0;

if (isLoggedIn()) {
    $user_id = (int) getUserId();
    $stmt = $conn->prepare("SELECT SUM(quantity) as total FROM cart WHERE user_id = ?");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result) {
        $data = $result->fetch_assoc();
        $count = $data['total'] ?? 0;
    }
}

echo json_encode(['count' => (int)$count]);
?>
