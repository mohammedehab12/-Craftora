<?php
require_once __DIR__ . '/config.php';

if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$errors = [];
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $errors[] = 'من فضلك أدخل البريد الإلكتروني وكلمة المرور.';
    } else {
        $stmt = $pdo->prepare('SELECT id, name, password FROM users WHERE email = :email');
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['user_name'] = $user['name'];

            header('Location: index.php');
            exit;
        } else {
            $errors[] = 'البريد الإلكتروني أو كلمة المرور غير صحيحة.';
        }
    }
}

$pageTitle  = 'Login - Craftora';
$activePage = 'login';
include __DIR__ . '/includes/header.php';
?>

<div class="auth-wrapper">
    <div class="auth-card">
        <div class="logo-icon"><i class="fa-solid fa-cube"></i></div>
        <h3 class="text-center fw-bold mb-1">Welcome Back</h3>
        <p class="text-center text-muted small mb-4">Log in to continue shopping.</p>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger small">
                <ul class="mb-0 ps-3">
                    <?php foreach ($errors as $err): ?>
                        <li><?php echo htmlspecialchars($err); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" action="login.php" novalidate>
            <div class="mb-3 input-icon-wrapper">
                <i class="fa-solid fa-envelope"></i>
                <input type="email" class="form-control" name="email" placeholder="Email address"
                       value="<?php echo htmlspecialchars($email); ?>" required>
            </div>
            <div class="mb-3 input-icon-wrapper">
                <i class="fa-solid fa-lock"></i>
                <input type="password" class="form-control" name="password" placeholder="Password" required>
            </div>
            <div class="d-flex justify-content-end mb-3">
                <a href="#" class="small text-muted">Forgot password?</a>
            </div>

            <button type="submit" class="btn btn-gradient w-100">Log In</button>
        </form>

        <p class="text-center small mt-4 mb-0">
            Don't have an account? <a href="register.php">Create one</a>
        </p>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
