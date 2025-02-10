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

// Get user data
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $first_name = htmlspecialchars($_POST['first_name'], ENT_QUOTES, 'UTF-8');
    $last_name = htmlspecialchars($_POST['last_name'], ENT_QUOTES, 'UTF-8');
    $phone = htmlspecialchars($_POST['phone'], ENT_QUOTES, 'UTF-8');
    $address = htmlspecialchars($_POST['address'], ENT_QUOTES, 'UTF-8');

    try {
        $sql = "UPDATE users SET 
                email = ?, 
                first_name = ?, 
                last_name = ?, 
                phone = ?, 
                address = ?
                WHERE id = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([$email, $first_name, $last_name, $phone, $address, $_SESSION['user_id']]);
        
        $message = "Profile updated successfully!";
        $messageType = 'success';
        
        // Refresh user data
        $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch();
    } catch(PDOException $e) {
        if ($e->getCode() == 23000) {
            $message = "Email already exists";
        } else {
            $message = "Update failed: " . $e->getMessage();
        }
        $messageType = 'error';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - User Management System</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="dashboard-container">
        <nav>
            <h2>My Profile</h2>
            <div class="nav-links">
                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <a href="dashboard.php">Dashboard</a>
                <?php endif; ?>
                <a href="change_password.php">Change Password</a>
                <a href="logout.php">Logout</a>
            </div>
        </nav>

        <div class="profile-container">
            <?php if ($message): ?>
                <p class="<?php echo $messageType; ?>"><?php echo htmlspecialchars($message); ?></p>
            <?php endif; ?>

            <div class="profile-header">
                <h3><?php echo htmlspecialchars($user['username']); ?></h3>
                <span class="role-badge <?php echo $user['role']; ?>"><?php echo ucfirst($user['role']); ?></span>
            </div>

            <form method="POST" action="" class="edit-form">
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required
                           value="<?php echo htmlspecialchars($user['email']); ?>">
                </div>

                <div class="form-group">
                    <label for="first_name">First Name:</label>
                    <input type="text" id="first_name" name="first_name"
                           value="<?php echo htmlspecialchars($user['first_name'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="last_name">Last Name:</label>
                    <input type="text" id="last_name" name="last_name"
                           value="<?php echo htmlspecialchars($user['last_name'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="phone">Phone:</label>
                    <input type="text" id="phone" name="phone"
                           value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="address">Address:</label>
                    <textarea id="address" name="address"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                </div>

                <div class="form-group">
                    <label>Account Created:</label>
                    <div class="static-field"><?php echo date('F j, Y', strtotime($user['created_at'])); ?></div>
                </div>

                <div class="form-group">
                    <label>Last Updated:</label>
                    <div class="static-field"><?php echo date('F j, Y', strtotime($user['updated_at'])); ?></div>
                </div>

                <button type="submit">Update Profile</button>
            </form>
        </div>
    </div>
</body>
</html> 