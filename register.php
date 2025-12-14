<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

// Only allow admins to register new users
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $role = $_POST['role'] ?? 'user';
    
    if ($username && $email && $password && $confirm_password && $role) {
        if ($password === $confirm_password) {
            if (registerUser($pdo, $username, $email, $password, $role)) {
                $success = 'User account created successfully!';
            } else {
                $error = 'Registration failed. Username or email may already exist.';
            }
        } else {
            $error = 'Passwords do not match.';
        }
    } else {
        $error = 'Please fill in all fields.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - ITicket</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="login-container">
        <form class="login-form" method="POST">
            <h2>Create New User Account</h2>
            <p style="text-align: center; color: #666; margin-bottom: 1rem;">Admin Panel - User Registration</p>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Confirm Password:</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            
            <div class="form-group">
                <label for="role">User Role:</label>
                <select id="role" name="role" required>
                    <option value="user">Regular User</option>
                    <option value="technician">Technician</option>
                    <option value="admin">Administrator</option>
                </select>
            </div>
            
            <button type="submit" class="btn btn-primary" style="width: 100%;">Create User</button>
            
            <p style="text-align: center; margin-top: 1rem;">
                <a href="admin.php">Back to Admin Panel</a>
            </p>
        </form>
    </div>
    
    <script src="assets/js/main.js"></script>
</body>
</html>