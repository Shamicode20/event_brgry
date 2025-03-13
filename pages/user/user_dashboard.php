<?php
session_start();
require '../../database/connection.php';

$user_id = $_SESSION['user_id']; // Assuming user ID is stored in session

try {
    $stmt = $conn->prepare("SELECT * FROM notifications WHERE user_id = :user_id ORDER BY created_at DESC");
    $stmt->execute(['user_id' => $user_id]);
    $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $_SESSION['error'] = "Error fetching notifications: " . $e->getMessage();
}
?>

<div class="notifications">
    <h4>Notifications</h4>
    <ul class="list-group">
        <?php foreach ($notifications as $notification): ?>
            <li class="list-group-item <?php echo ($notification['status'] == 'unread') ? 'bg-light' : ''; ?>">
                <?php echo htmlspecialchars($notification['message']); ?>
                <small class="text-muted d-block"><?php echo $notification['created_at']; ?></small>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
<?php
if (!empty($notifications)) {
    $stmt = $conn->prepare("UPDATE notifications SET status = 'read' WHERE user_id = :user_id");
    $stmt->execute(['user_id' => $user_id]);
}
?>
