<?php
require_once 'config.php';

if (isLoggedIn()) {
    redirect('index.php');
}

$error = '';
$notice = '';

if (($_GET['notice'] ?? '') === 'already_registered') {
    $notice = 'This email is already registered. Please log in.';
} elseif (($_GET['error'] ?? '') === 'account_not_found') {
    $notice = 'Your session has expired or your account no longer exists. Please log in again.';
}

$redirect = $_GET['redirect'] ?? 'index.php';
// Only allow local relative paths to prevent open-redirect attacks
if (!preg_match('/^[a-zA-Z0-9_\-]+\.php(\?[a-zA-Z0-9_\-=&%.]*)?$/', $redirect)) {
    $redirect = 'index.php';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'Please fill in all fields';
    } else {
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];

                redirect($redirect);
            } else {
                $error = 'Invalid email or password';
            }
        } else {
            $error = 'Invalid email or password';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Craftora</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="auth-page">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card shadow-lg border-0 auth-card">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <img src="images/logo.png" alt="Craftora" class="auth-logo mb-2" onerror="this.remove()">
                            <i class="fas fa-user-circle text-primary fa-3x mb-3"></i>
                            <h2 class="fw-bold">Welcome Back</h2>
                            <p class="text-muted">Login to continue shopping</p>
                        </div>

                        <?php if ($notice): ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> <?php echo htmlspecialchars($notice); ?>
                        </div>
                        <?php endif; ?>

                        <?php if ($error): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
                        </div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                </div>
                            </div>

                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="remember">
                                <label class="form-check-label" for="remember">Remember me</label>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 btn-lg mb-3">
                                <i class="fas fa-sign-in-alt"></i> Login
                            </button>

                            <div class="text-center">
                                <p class="text-muted">Don't have an account?
                                    <a href="register.php" class="text-primary fw-bold">Sign Up</a>
                                </p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
