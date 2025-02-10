<?php
ob_start();
session_start();
require_once 'config/db_config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$message = '';
$user = null;

if (isset($_GET['id'])) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $user = $stmt->fetch();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_EMAIL);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $first_name = htmlspecialchars($_POST['first_name'], ENT_QUOTES, 'UTF-8');
    $last_name = htmlspecialchars($_POST['last_name'], ENT_QUOTES, 'UTF-8');
    $phone = htmlspecialchars($_POST['phone'], ENT_QUOTES, 'UTF-8');
    $address = htmlspecialchars($_POST['address'], ENT_QUOTES, 'UTF-8');
    $role = $_POST['role'];

    try {
        $sql = "UPDATE users SET 
                username = ?, 
                email = ?, 
                first_name = ?, 
                last_name = ?, 
                phone = ?, 
                address = ?, 
                role = ? 
                WHERE id = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([$username, $email, $first_name, $last_name, $phone, $address, $role, $id]);
        
        $message = "User updated successfully!";
        header("Location: dashboard.php");
        exit();
    } catch(PDOException $e) {
        if ($e->getCode() == 23000) {
            $message = "Username or email already exists!";
        } else {
            $message = "Update failed: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User - User Management System</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="dashboard-container">
        <nav>
            <h2>Edit User</h2>
            <div class="nav-links">
                <a href="dashboard.php">Back to Dashboard</a>
                <a href="logout.php">Logout</a>
            </div>
        </nav>

        <?php if ($user): ?>
        <div class="edit-form-container">
            <?php if ($message): ?>
                <p class="error"><?php echo htmlspecialchars($message); ?></p>
            <?php endif; ?>

            <form method="POST" action="" class="edit-form">
                <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
                
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" required 
                           value="<?php echo htmlspecialchars($user['username']); ?>">
                </div>

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
                    <label for="role">Role:</label>
                    <select id="role" name="role">
                        <option value="user" <?php echo $user['role'] === 'user' ? 'selected' : ''; ?>>User</option>
                        <option value="admin" <?php echo $user['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                    </select>
                </div>

                <button type="submit">Update User</button>
            </form>
        </div>
        <?php else: ?>
            <p class="error">User not found.</p>
        <?php endif; ?>
    </div>
</body>
</html> 