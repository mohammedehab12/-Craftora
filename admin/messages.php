<?php
require_once __DIR__ . '/../includes/admin_auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    $id = (int) ($_POST['id'] ?? 0);
    $stmt = $pdo->prepare('DELETE FROM contact_messages WHERE id = :id');
    $stmt->execute(['id' => $id]);
    header('Location: messages.php?deleted=1');
    exit;
}

$messages = $pdo->query('SELECT * FROM contact_messages ORDER BY created_at DESC')->fetchAll();

$pageTitle       = 'Messages - Craftora Admin';
$activeAdminPage = 'messages';
include __DIR__ . '/../includes/admin_header.php';
?>

<?php if (isset($_GET['deleted'])): ?>
    <div class="alert alert-success">Message deleted.</div>
<?php endif; ?>

<div class="admin-card">
    <h6 class="fw-bold mb-3">Contact Messages (<?php echo count($messages); ?>)</h6>

    <?php if (empty($messages)): ?>
        <p class="text-muted small mb-0">No messages yet.</p>
    <?php else: ?>
        <?php foreach ($messages as $msg): ?>
            <div class="border rounded-3 p-3 mb-3">
                <div class="d-flex justify-content-between flex-wrap gap-2 mb-2">
                    <div>
                        <div class="fw-semibold"><?php echo htmlspecialchars($msg['name']); ?></div>
                        <div class="small text-muted"><?php echo htmlspecialchars($msg['email']); ?></div>
                    </div>
                    <div class="text-end">
                        <div class="small text-muted"><?php echo date('M d, Y - H:i', strtotime($msg['created_at'])); ?></div>
                        <form method="POST" action="messages.php" onsubmit="return confirm('Delete this message?');">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?php echo $msg['id']; ?>">
                            <button type="submit" class="btn btn-sm btn-outline-danger mt-1">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
                <p class="mb-0 small"><?php echo nl2br(htmlspecialchars($msg['message'])); ?></p>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../includes/admin_footer.php'; ?>
