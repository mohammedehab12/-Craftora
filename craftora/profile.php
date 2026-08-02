<?php
require_once __DIR__ . '/config.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$userId = currentUserId();
$errors = [];
$success = false;

$stmt = $pdo->prepare('SELECT name, email, phone, address, created_at FROM users WHERE id = :id');
$stmt->execute(['id' => $userId]);
$user = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name'] ?? '');
    $phone   = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $newPassword = $_POST['new_password'] ?? '';

    if ($name === '') {
        $errors[] = 'الاسم مطلوب.';
    }

    if (empty($errors)) {
        if ($newPassword !== '') {
            if (strlen($newPassword) < 6) {
                $errors[] = 'كلمة المرور الجديدة يجب أن تكون 6 أحرف على الأقل.';
            } else {
                $hashed = password_hash($newPassword, PASSWORD_BCRYPT);
                $stmt = $pdo->prepare('UPDATE users SET name = :name, phone = :phone, address = :address, password = :password WHERE id = :id');
                $stmt->execute([
                    'name'     => $name,
                    'phone'    => $phone ?: null,
                    'address'  => $address ?: null,
                    'password' => $hashed,
                    'id'       => $userId,
                ]);
            }
        } else {
            $stmt = $pdo->prepare('UPDATE users SET name = :name, phone = :phone, address = :address WHERE id = :id');
            $stmt->execute([
                'name'    => $name,
                'phone'   => $phone ?: null,
                'address' => $address ?: null,
                'id'      => $userId,
            ]);
        }

        if (empty($errors)) {
            $_SESSION['user_name'] = $name;
            $success = true;

            $stmt = $pdo->prepare('SELECT name, email, phone, address, created_at FROM users WHERE id = :id');
            $stmt->execute(['id' => $userId]);
            $user = $stmt->fetch();
        }
    }
}

$pageTitle  = 'My Profile - Craftora';
$activePage = 'profile';
include __DIR__ . '/includes/header.php';
?>

<section class="py-5">
    <div class="container" style="max-width: 640px;">
        <h1 class="section-title">My Profile</h1>

        <?php if ($success): ?>
            <div class="alert alert-success">Profile updated successfully.</div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul class="mb-0 ps-3">
                    <?php foreach ($errors as $err): ?>
                        <li><?php echo htmlspecialchars($err); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="summary-box">
            <form method="POST" action="profile.php">
                <div class="mb-3">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
                    <div class="form-text">Email address cannot be changed.</div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Phone</label>
                    <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Address</label>
                    <textarea name="address" class="form-control" rows="2"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                </div>
                <div class="mb-4">
                    <label class="form-label">New Password</label>
                    <input type="password" name="new_password" class="form-control" placeholder="Leave blank to keep current password">
                </div>

                <button type="submit" class="btn btn-gradient w-100">Save Changes</button>
            </form>
        </div>

        <p class="text-muted small text-center mt-3">
            Member since <?php echo date('F Y', strtotime($user['created_at'])); ?>
        </p>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
