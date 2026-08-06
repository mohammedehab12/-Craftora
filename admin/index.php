<?php
require_once __DIR__ . '/../includes/admin_auth.php';

$totalProducts = (int) $pdo->query('SELECT COUNT(*) FROM products')->fetchColumn();
$totalOrders   = (int) $pdo->query('SELECT COUNT(*) FROM orders')->fetchColumn();
$totalUsers    = (int) $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
$totalRevenue  = (float) $pdo->query("SELECT COALESCE(SUM(total_price),0) FROM orders WHERE status != 'cancelled'")->fetchColumn();
$unreadMsgs    = (int) $pdo->query('SELECT COUNT(*) FROM contact_messages')->fetchColumn();
$lowStock      = (int) $pdo->query('SELECT COUNT(*) FROM products WHERE stock <= 5')->fetchColumn();

$recentOrders = $pdo->query('
    SELECT o.id, o.total_price, o.status, o.order_date, u.name AS customer_name
    FROM orders o
    JOIN users u ON u.id = o.user_id
    ORDER BY o.order_date DESC
    LIMIT 5
')->fetchAll();

$pageTitle       = 'Dashboard - Craftora Admin';
$activeAdminPage = 'dashboard';
include __DIR__ . '/../includes/admin_header.php';
?>

<div class="row g-3 mb-4">
    <div class="col-md-3 col-6">
        <div class="stat-card">
            <div class="stat-icon"><i class="fa-solid fa-box"></i></div>
            <div>
                <div class="stat-value"><?php echo $totalProducts; ?></div>
                <div class="stat-label">Products</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="stat-card">
            <div class="stat-icon"><i class="fa-solid fa-receipt"></i></div>
            <div>
                <div class="stat-value"><?php echo $totalOrders; ?></div>
                <div class="stat-label">Orders</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="stat-card">
            <div class="stat-icon"><i class="fa-solid fa-users"></i></div>
            <div>
                <div class="stat-value"><?php echo $totalUsers; ?></div>
                <div class="stat-label">Customers</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="stat-card">
            <div class="stat-icon"><i class="fa-solid fa-sack-dollar"></i></div>
            <div>
                <div class="stat-value">$<?php echo number_format($totalRevenue, 2); ?></div>
                <div class="stat-label">Revenue</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="admin-card d-flex align-items-center justify-content-between">
            <div>
                <div class="fw-bold">Low Stock Products</div>
                <div class="text-muted small">Products with 5 units or less</div>
            </div>
            <span class="badge bg-danger fs-6"><?php echo $lowStock; ?></span>
        </div>
    </div>
    <div class="col-md-6">
        <div class="admin-card d-flex align-items-center justify-content-between">
            <div>
                <div class="fw-bold">Contact Messages</div>
                <div class="text-muted small">Total messages received</div>
            </div>
            <span class="badge bg-info fs-6"><?php echo $unreadMsgs; ?></span>
        </div>
    </div>
</div>

<div class="admin-card">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="fw-bold mb-0">Recent Orders</h6>
        <a href="orders.php" class="small">View all</a>
    </div>

    <?php if (empty($recentOrders)): ?>
        <p class="text-muted small mb-0">No orders yet.</p>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table admin-table mb-0">
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Customer</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th class="text-end">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentOrders as $order): ?>
                        <tr>
                            <td>#<?php echo $order['id']; ?></td>
                            <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                            <td><?php echo date('M d, Y', strtotime($order['order_date'])); ?></td>
                            <td><span class="badge bg-secondary text-capitalize"><?php echo htmlspecialchars($order['status']); ?></span></td>
                            <td class="text-end">$<?php echo number_format($order['total_price'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../includes/admin_footer.php'; ?>
