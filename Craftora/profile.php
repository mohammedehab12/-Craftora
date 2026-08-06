<?php
require_once 'config.php';

if (!isLoggedIn()) {
    redirect('register.php');
}

$user_id = (int) getUserId();

// ======================
// Get user information
// ======================
$user_stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$result = $user_stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    // The session points to a user_id that no longer exists in the
    // database (e.g. the account was deleted). Log the session out
    // instead of showing a blank/ghost profile form.
    session_unset();
    session_destroy();
    session_start();
    redirect('login.php?error=account_not_found');
}

$success = '';
$error = '';

// ======================
// Update Profile
// ======================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = sanitize($_POST['name'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    $address = sanitize($_POST['address'] ?? '');

    if (empty($name)) {

        $error = "Name is required.";

    } else {

        $update_stmt = $conn->prepare("
            UPDATE users
            SET name = ?, phone = ?, address = ?
            WHERE id = ?
        ");

        $update_stmt->bind_param("sssi", $name, $phone, $address, $user_id);

        if ($update_stmt->execute()) {

            $_SESSION['user_name'] = $name;

            $success = "Profile updated successfully.";

            // Refresh Data
            $refresh_stmt = $conn->prepare("SELECT * FROM users WHERE id=?");
            $refresh_stmt->bind_param("i", $user_id);
            $refresh_stmt->execute();

            $refresh = $refresh_stmt->get_result();

            if ($refresh->num_rows > 0) {
                $user = $refresh->fetch_assoc();
            }

        } else {

            $error = "Failed to update profile.";

        }
    }
}

// ======================
// Order Statistics
// ======================
$orders_stmt = $conn->prepare("
    SELECT
        COUNT(*) AS total_orders,
        COALESCE(SUM(total_price),0) AS total_spent
    FROM orders
    WHERE user_id = ?
");

$orders_stmt->bind_param("i", $user_id);
$orders_stmt->execute();

$orders_stats = $orders_stmt->get_result()->fetch_assoc();

if (!$orders_stats) {

    $orders_stats = [
        'total_orders' => 0,
        'total_spent' => 0
    ];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Craftora</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <?php include 'includes/header.php'; ?>

    <div class="container my-5">
        <h1 class="display-5 fw-bold mb-4">My Profile</h1>

        <div class="row">
            <div class="col-lg-4 mb-4">
                <div class="profile-card">
                    <div class="text-center mb-4">
                        <div class="avatar-circle mb-3">
                            <i class="fas fa-user fa-3x text-primary"></i>
                        </div>
                        <h4 class="fw-bold"><?php echo htmlspecialchars($user['name']); ?></h4>
                        <p class="text-muted"><?php echo htmlspecialchars($user['email']); ?></p>
                        <span class="badge bg-primary">Member since <?php echo date('M Y', strtotime($user['created_at'])); ?></span>
                    </div>

                    <div class="list-group">
                        <a href="profile.php" class="list-group-item list-group-item-action active">
                            <i class="fas fa-user me-2"></i> Profile Information
                        </a>
                        <a href="orders.php" class="list-group-item list-group-item-action">
                            <i class="fas fa-shopping-bag me-2"></i> My Orders
                            <?php if ($orders_stats['total_orders'] > 0): ?>
                                <span class="badge bg-primary float-end"><?php echo $orders_stats['total_orders']; ?></span>
                            <?php endif; ?>
                        </a>
                        <a href="logout.php" class="list-group-item list-group-item-action text-danger">
                            <i class="fas fa-sign-out-alt me-2"></i> Logout
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="profile-card mb-4">
                    <h4 class="fw-bold mb-4"><i class="fas fa-chart-line text-primary"></i> Your Statistics</h4>

                    <div class="row text-center">
                        <div class="col-md-6 mb-3">
                            <div class="stat-box p-3">
                                <i class="fas fa-shopping-bag fa-2x text-primary mb-2"></i>
                                <h4 class="fw-bold"><?php echo $orders_stats['total_orders'] ?? 0; ?></h4>
                                <p class="text-muted mb-0">Total Orders</p>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="stat-box p-3">
                                <i class="fas fa-money-bill-wave fa-2x text-success mb-2"></i>
                                <h4 class="fw-bold"><?php echo formatPrice($orders_stats['total_spent'] ?? 0); ?></h4>
                                <p class="text-muted mb-0">Total Spent</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="profile-card">
                    <h4 class="fw-bold mb-4"><i class="fas fa-edit text-primary"></i> Edit Profile</h4>

                    <?php if ($success): ?>
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success); ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($error): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="name" class="form-label">Full Name *</label>
                            <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
                            <small class="text-muted">Email cannot be changed</small>
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="tel" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label">Address</label>
                            <textarea class="form-control" id="address" name="address" rows="3"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-save"></i> Save Changes
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>