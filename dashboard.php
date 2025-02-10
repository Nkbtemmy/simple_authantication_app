<?php
ob_start();
session_start();
require_once 'config/db_config.php';

// Strict role check for admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    if (isset($_SESSION['user_id'])) {
        header("Location: profile.php"); // Redirect logged-in users to their profile
    } else {
        header("Location: login.php"); // Redirect non-logged-in users to login
    }
    exit();
}

$stmt = $conn->query("SELECT * FROM users WHERE id != " . $_SESSION['user_id']);
$users = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - User Management System</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="js/script.js" defer></script>
</head>
<body>
    <div class="dashboard-container">
        <nav>
            <h2>User Management Dashboard</h2>
            <div class="nav-links">
                <a href="profile.php">My Profile</a>
                <a href="change_password.php">Change Password</a>
                <a href="logout.php">Logout</a>
            </div>
        </nav>
        
        <div class="main-content">
            <button onclick="showAddUserForm()" class="add-user-btn">Add New User</button>
            
            <table class="users-table">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['username'] ?? ''); ?></td>
                        <td><?php 
                            $firstName = htmlspecialchars($user['first_name'] ?? '');
                            $lastName = htmlspecialchars($user['last_name'] ?? '');
                            echo trim("$firstName $lastName");
                        ?></td>
                        <td><?php echo htmlspecialchars($user['email'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($user['phone'] ?? ''); ?></td>
                        <td>
                            <button onclick="editUser(<?php echo $user['id']; ?>)">Edit</button>
                            <button onclick="deleteUser(<?php echo $user['id']; ?>)">Delete</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html> 