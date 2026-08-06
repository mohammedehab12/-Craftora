<?php
require_once __DIR__ . '/config.php';

$errors = [];
$success = false;
$name = $email = $message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if ($name === '') {
        $errors[] = 'الاسم مطلوب.';
    }
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'من فضلك أدخل بريد إلكتروني صحيح.';
    }
    if ($message === '') {
        $errors[] = 'الرسالة مطلوبة.';
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare('INSERT INTO contact_messages (name, email, message) VALUES (:name, :email, :message)');
        $stmt->execute(['name' => $name, 'email' => $email, 'message' => $message]);

        $success = true;
        $name = $email = $message = '';
    }
}

$pageTitle  = 'Contact Us - Craftora';
$activePage = 'contact';
include __DIR__ . '/includes/header.php';
?>

<section class="py-5">
    <div class="container" style="max-width: 640px;">
        <h1 class="section-title">Contact Us</h1>
        <p class="text-center text-muted mb-5">Have a question or feedback? We'd love to hear from you.</p>

        <?php if ($success): ?>
            <div class="alert alert-success text-center">
                <i class="fa-solid fa-circle-check me-2"></i>Your message has been sent. We'll get back to you soon!
            </div>
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
            <form method="POST" action="contact.php">
                <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($name); ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($email); ?>" required>
                </div>
                <div class="mb-4">
                    <label class="form-label">Message</label>
                    <textarea name="message" class="form-control" rows="5" required><?php echo htmlspecialchars($message); ?></textarea>
                </div>

                <button type="submit" class="btn btn-gradient w-100">Send Message</button>
            </form>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
