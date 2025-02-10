<?php
ob_start();
session_start();
require_once 'config/db_config.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_EMAIL);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $first_name = htmlspecialchars($_POST['first_name'], ENT_QUOTES, 'UTF-8');
    $last_name = htmlspecialchars($_POST['last_name'], ENT_QUOTES, 'UTF-8');

    // Validation
    if ($password !== $confirm_password) {
        $message = "Passwords do not match!";
    } elseif (strlen($password) < 8) {
        $message = "Password must be at least 8 characters long!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid email format!";
    } elseif (!preg_match("/^[a-zA-Z0-9_]+$/", $username)) {
        $message = "Username can only contain letters, numbers, and underscores!";
    } else {
        try {
            // Begin transaction
            $conn->beginTransaction();

            // Insert into users table
            $stmt = $conn->prepare("INSERT INTO users (username, email, password, role, first_name, last_name) 
                                   VALUES (?, ?, ?, 'user', ?, ?)");
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt->execute([$username, $email, $hashedPassword, $first_name, $last_name]);
            
            // Get the user ID for profile creation
            $user_id = $conn->lastInsertId();

            // Insert into user_profiles table
            $stmt = $conn->prepare("INSERT INTO user_profiles (user_id, first_name, last_name) VALUES (?, ?, ?)");
            $stmt->execute([$user_id, $first_name, $last_name]);

            // Commit transaction
            $conn->commit();

            $_SESSION['signup_success'] = true;
            header("Location: login.php");
            exit();

        } catch(PDOException $e) {
            // Rollback transaction on error
            $conn->rollBack();
            if ($e->getCode() == 23000) { // Duplicate entry error
                $message = "Username or email already exists!";
            } else {
                $message = "Registration failed: " . $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - User Management System</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="signup-container">
        <h2>Create Account</h2>
        <?php if ($message): ?>
            <p class="error"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>

        <form method="POST" action="" class="signup-form">
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
                <label for="first_name">First Name:</label>
                <input type="text" id="first_name" name="first_name" required
                       value="<?php echo isset($_POST['first_name']) ? htmlspecialchars($_POST['first_name']) : ''; ?>">
            </div>

            <div class="form-group">
                <label for="last_name">Last Name:</label>
                <input type="text" id="last_name" name="last_name" required
                       value="<?php echo isset($_POST['last_name']) ? htmlspecialchars($_POST['last_name']) : ''; ?>">
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

            <button type="submit">Sign Up</button>

            <div class="form-footer">
                Already have an account? <a href="login.php">Login here</a>
            </div>
        </form>
    </div>

    <script>
        // Client-side password validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;

            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Passwords do not match!');
            }

            if (password.length < 8) {
                e.preventDefault();
                alert('Password must be at least 8 characters long!');
            }
        });
    </script>
</body>
</html> 