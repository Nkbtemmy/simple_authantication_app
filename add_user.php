<?php
ob_start();
session_start();
require_once 'config/db_config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_EMAIL);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $first_name = htmlspecialchars($_POST['first_name'], ENT_QUOTES, 'UTF-8');
    $last_name = htmlspecialchars($_POST['last_name'], ENT_QUOTES, 'UTF-8');
    $phone = htmlspecialchars($_POST['phone'], ENT_QUOTES, 'UTF-8');
    $address = htmlspecialchars($_POST['address'], ENT_QUOTES, 'UTF-8');
    $role = $_POST['role'];

    if ($password !== $confirm_password) {
        $message = "Passwords do not match";
        $messageType = 'error';
    } elseif (strlen($password) < 8) {
        $message = "Password must be at least 8 characters long";
        $messageType = 'error';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid email format";
        $messageType = 'error';
    } else {
        try {
            $stmt = $conn->prepare("INSERT INTO users (username, email, password, role, first_name, last_name, phone, address) 
                                  VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt->execute([$username, $email, $hashed_password, $role, $first_name, $last_name, $phone, $address]);
            
            header("Location: dashboard.php");
            exit();
        } catch(PDOException $e) {
            if ($e->getCode() == 23000) {
                $message = "Username or email already exists";
            } else {
                $message = "Error creating user: " . $e->getMessage();
            }
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
    <title>Add User - User Management System</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="dashboard-container">
        <nav>
            <h2>Add New User</h2>
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
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" required 
                           value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
                </div>

                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required
                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                </div>

                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                    <small>Must be at least 8 characters long</small>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm Password:</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>

                <div class="form-group">
                    <label for="first_name">First Name:</label>
                    <input type="text" id="first_name" name="first_name"
                           value="<?php echo isset($_POST['first_name']) ? htmlspecialchars($_POST['first_name']) : ''; ?>">
                </div>

                <div class="form-group">
                    <label for="last_name">Last Name:</label>
                    <input type="text" id="last_name" name="last_name"
                           value="<?php echo isset($_POST['last_name']) ? htmlspecialchars($_POST['last_name']) : ''; ?>">
                </div>

                <div class="form-group">
                    <label for="phone">Phone:</label>
                    <input type="text" id="phone" name="phone"
                           value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>">
                </div>

                <div class="form-group">
                    <label for="address">Address:</label>
                    <textarea id="address" name="address"><?php echo isset($_POST['address']) ? htmlspecialchars($_POST['address']) : ''; ?></textarea>
                </div>

                <div class="form-group">
                    <label for="role">Role:</label>
                    <select id="role" name="role">
                        <option value="user" selected>User</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>

                <button type="submit">Create User</button>
            </form>
        </div>
    </div>
</body>
</html> 