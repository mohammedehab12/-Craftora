<?php
require_once __DIR__ . '/../config.php';

if (isAdminLoggedIn()) {
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
        $stmt = $pdo->prepare('SELECT id, name, password FROM admins WHERE email = :email');
        $stmt->execute(['email' => $email]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($password, $admin['password'])) {
            session_regenerate_id(true);
            $_SESSION['admin_id']   = $admin['id'];
            $_SESSION['admin_name'] = $admin['name'];

            header('Location: index.php');
            exit;
        } else {
            $errors[] = 'البريد الإلكتروني أو كلمة المرور غير صحيحة.';
        }
    }
}

$pageTitle = 'Admin Login - Craftora';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="<?php echo BASE_URL; ?>css/admin.css" rel="stylesheet">
</head>
<body>

<div class="admin-login-wrapper">
    <div class="admin-login-card">
        <div class="logo-icon"><i class="fa-solid fa-user-shield"></i></div>
        <h4 class="text-center fw-bold mb-1">Admin Login</h4>
        <p class="text-center text-muted small mb-4">Craftora management panel</p>

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
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
            </div>
            <div class="mb-4">
                <label class="form-label">Password</label>
                <input type="password" class="form-control" name="password" required>
            </div>
            <button type="submit" class="btn btn-admin w-100">Log In</button>
        </form>
    </div>
</div>

</body>
</html>
