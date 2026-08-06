<?php
require_once __DIR__ . '/config.php';

if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$errors = [];
$name = $email = $phone = $address = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $phone    = trim($_POST['phone'] ?? '');
    $address  = trim($_POST['address'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';

    if ($name === '') {
        $errors[] = 'الاسم مطلوب.';
    }
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'من فضلك أدخل بريد إلكتروني صحيح.';
    }
    if (strlen($password) < 6) {
        $errors[] = 'كلمة المرور يجب أن تكون 6 أحرف على الأقل.';
    }
    if ($password !== $confirm) {
        $errors[] = 'كلمتا المرور غير متطابقتين.';
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = :email');
        $stmt->execute(['email' => $email]);
        if ($stmt->fetch()) {
            $errors[] = 'هذا البريد الإلكتروني مسجل بالفعل.';
        }
    }

    if (empty($errors)) {
        $hashed = password_hash($password, PASSWORD_BCRYPT);

        $stmt = $pdo->prepare('
            INSERT INTO users (name, email, password, phone, address)
            VALUES (:name, :email, :password, :phone, :address)
        ');
        $stmt->execute([
            'name'     => $name,
            'email'    => $email,
            'password' => $hashed,
            'phone'    => $phone ?: null,
            'address'  => $address ?: null,
        ]);

        $_SESSION['user_id']   = $pdo->lastInsertId();
        $_SESSION['user_name'] = $name;

        header('Location: index.php');
        exit;
    }
}

$pageTitle  = 'Register - Craftora';
$activePage = 'register';
include __DIR__ . '/includes/header.php';
?>

<div class="auth-wrapper">
    <div class="auth-card">
        <div class="logo-icon"><i class="fa-solid fa-cube"></i></div>
        <h3 class="text-center fw-bold mb-1">Create Account</h3>
        <p class="text-center text-muted small mb-4">Join Craftora and start shopping handmade goods.</p>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger small">
                <ul class="mb-0 ps-3">
                    <?php foreach ($errors as $err): ?>
                        <li><?php echo htmlspecialchars($err); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" action="register.php" novalidate>
            <div class="mb-3 input-icon-wrapper">
                <i class="fa-solid fa-user"></i>
                <input type="text" class="form-control" name="name" placeholder="Full name"
                       value="<?php echo htmlspecialchars($name); ?>" required>
            </div>
            <div class="mb-3 input-icon-wrapper">
                <i class="fa-solid fa-envelope"></i>
                <input type="email" class="form-control" name="email" placeholder="Email address"
                       value="<?php echo htmlspecialchars($email); ?>" required>
            </div>
            <div class="mb-3 input-icon-wrapper">
                <i class="fa-solid fa-phone"></i>
                <input type="text" class="form-control" name="phone" placeholder="Phone (optional)"
                       value="<?php echo htmlspecialchars($phone); ?>">
            </div>
            <div class="mb-3 input-icon-wrapper">
                <i class="fa-solid fa-location-dot"></i>
                <input type="text" class="form-control" name="address" placeholder="Address (optional)"
                       value="<?php echo htmlspecialchars($address); ?>">
            </div>
            <div class="mb-3 input-icon-wrapper">
                <i class="fa-solid fa-lock"></i>
                <input type="password" class="form-control" name="password" placeholder="Password" required>
            </div>
            <div class="mb-3 input-icon-wrapper">
                <i class="fa-solid fa-lock"></i>
                <input type="password" class="form-control" name="confirm_password" placeholder="Confirm password" required>
            </div>

            <button type="submit" class="btn btn-gradient w-100 mt-2">Create Account</button>
        </form>

        <p class="text-center small mt-4 mb-0">
            Already have an account? <a href="login.php">Log in</a>
        </p>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
