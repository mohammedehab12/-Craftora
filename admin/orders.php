<?php
require_once __DIR__ . '/../includes/admin_auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'update_status') {
    $orderId   = (int) ($_POST['order_id'] ?? 0);
    $newStatus = $_POST['status'] ?? '';

    $validStatuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
    if (in_array($newStatus, $validStatuses, true)) {
        $stmt = $pdo->prepare('UPDATE orders SET status = :status WHERE id = :id');
        $stmt->execute(['status' => $newStatus, 'id' => $orderId]);
    }
    header('Location: orders.php?updated=1');
    exit;
}

$statusFilter = trim($_GET['status'] ?? '');

$sql = '
    SELECT o.*, u.name AS customer_name, u.email AS customer_email
    FROM orders o
    JOIN users u ON u.id = o.user_id
    WHERE 1=1
';
$params = [];
if ($statusFilter !== '') {
    $sql .= ' AND o.status = :status';
    $params['status'] = $statusFilter;
}
$sql .= ' ORDER BY o.order_date DESC';

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$orders = $stmt->fetchAll();

$itemsStmt = $pdo->prepare('
    SELECT oi.quantity, oi.price, p.name
    FROM order_items oi
    JOIN products p ON p.id = oi.product_id
    WHERE oi.order_id = :oid
');

$pageTitle       = 'Orders - Craftora Admin';
$activeAdminPage = 'orders';
include __DIR__ . '/../includes/admin_header.php';
?>

<?php if (isset($_GET['updated'])): ?>
    <div class="alert alert-success">Order status updated.</div>
<?php endif; ?>

<div class="admin-card mb-4">
    <form method="GET" action="orders.php" class="d-flex gap-2 align-items-end flex-wrap">
        <div>
            <label class="form-label small mb-1">Filter by status</label>
            <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">All</option>
                <?php foreach (['pending', 'processing', 'shipped', 'delivered', 'cancelled'] as $s): ?>
                    <option value="<?php echo $s; ?>" <?php echo $statusFilter === $s ? 'selected' : ''; ?>><?php echo ucfirst($s); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </form>
</div>

<div class="admin-card">
    <h6 class="fw-bold mb-3">Orders (<?php echo count($orders); ?>)</h6>

    <?php if (empty($orders)): ?>
        <p class="text-muted small mb-0">No orders found.</p>
    <?php else: ?>
        <div class="accordion" id="ordersAccordion">
            <?php foreach ($orders as $i => $order): ?>
                <?php
                    $itemsStmt->execute(['oid' => $order['id']]);
                    $items = $itemsStmt->fetchAll();
                    $statusClass = match ($order['status']) {
                        'delivered' => 'success',
                        'cancelled' => 'danger',
                        'shipped'   => 'info',
                        'processing'=> 'primary',
                        default     => 'warning',
                    };
                ?>
                <div class="accordion-item mb-2">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#order<?php echo $order['id']; ?>">
                            <div class="d-flex justify-content-between align-items-center w-100 me-3 flex-wrap gap-2">
                                <span>#<?php echo $order['id']; ?> — <?php echo htmlspecialchars($order['customer_name']); ?></span>
                                <span class="badge bg-<?php echo $statusClass; ?> text-capitalize"><?php echo $order['status']; ?></span>
                                <span class="fw-bold">$<?php echo number_format($order['total_price'], 2); ?></span>
                            </div>
                        </button>
                    </h2>
                    <div id="order<?php echo $order['id']; ?>" class="accordion-collapse collapse" data-bs-parent="#ordersAccordion">
                        <div class="accordion-body">
                            <p class="small text-muted mb-1">
                                <strong>Customer:</strong> <?php echo htmlspecialchars($order['customer_name']); ?>
                                (<?php echo htmlspecialchars($order['customer_email']); ?>)
                            </p>
                            <p class="small text-muted mb-1">
                                <strong>Shipping Address:</strong> <?php echo htmlspecialchars($order['shipping_address']); ?>
                            </p>
                            <p class="small text-muted mb-3">
                                <strong>Payment:</strong> <?php echo str_replace('_', ' ', htmlspecialchars($order['payment_method'])); ?>
                                &nbsp;|&nbsp; <strong>Date:</strong> <?php echo date('M d, Y - H:i', strtotime($order['order_date'])); ?>
                            </p>

                            <table class="table table-sm mb-3">
                                <thead><tr><th>Product</th><th>Qty</th><th class="text-end">Price</th></tr></thead>
                                <tbody>
                                    <?php foreach ($items as $item): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($item['name']); ?></td>
                                            <td><?php echo $item['quantity']; ?></td>
                                            <td class="text-end">$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>

                            <form method="POST" action="orders.php" class="d-flex gap-2">
                                <input type="hidden" name="action" value="update_status">
                                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                <select name="status" class="form-select form-select-sm" style="max-width: 200px;">
                                    <?php foreach (['pending', 'processing', 'shipped', 'delivered', 'cancelled'] as $s): ?>
                                        <option value="<?php echo $s; ?>" <?php echo $order['status'] === $s ? 'selected' : ''; ?>><?php echo ucfirst($s); ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="submit" class="btn btn-sm btn-admin">Update Status</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../includes/admin_footer.php'; ?>
