<?php
ob_start();
session_start();
require_once 'config/db_config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Get user's current password
    $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();

    if (!password_verify($current_password, $user['password'])) {
        $message = "Current password is incorrect";
        $messageType = 'error';
    } elseif ($new_password !== $confirm_password) {
        $message = "New passwords do not match";
        $messageType = 'error';
    } elseif (strlen($new_password) < 8) {
        $message = "Password must be at least 8 characters long";
        $messageType = 'error';
    } else {
        try {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->execute([$hashed_password, $_SESSION['user_id']]);
            
            $message = "Password changed successfully!";
            $messageType = 'success';
        } catch(PDOException $e) {
            $message = "Error changing password: " . $e->getMessage();
            $messageType = 'error';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password - User Management System</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="dashboard-container">
        <nav>
            <h2>Change Password</h2>
            <div class="nav-links">
                <a href="dashboard.php">Back to Dashboard</a>
                <a href="logout.php">Logout</a>
            </div>
        </nav>

        <div class="edit-form-container">
            <?php if ($message): ?>
                <p class="<?php echo $messageType; ?>"><?php echo htmlspecialchars($message); ?></p>
            <?php endif; ?>

            <form method="POST" action="" class="edit-form">
                <div class="form-group">
                    <label for="current_password">Current Password:</label>
                    <input type="password" id="current_password" name="current_password" required>
                </div>

                <div class="form-group">
                    <label for="new_password">New Password:</label>
                    <input type="password" id="new_password" name="new_password" required>
                    <small>Must be at least 8 characters long</small>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm New Password:</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>

                <button type="submit">Change Password</button>
            </form>
        </div>
    </div>
</body>
</html> 