<?php
require_once 'config/database.php';

// This script creates or updates an admin user
// Run this once and then delete the file for security

$username = 'admin';
$email = 'admin@company.com';
$password = 'admin123'; // Change this to your desired password
$role = 'admin';

// Hash the password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

try {
    // Check if admin user already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $existing_user = $stmt->fetch();
    
    if ($existing_user) {
        // Update existing admin user
        $stmt = $pdo->prepare("UPDATE users SET email = ?, password = ?, role = ? WHERE username = ?");
        $stmt->execute([$email, $hashed_password, $role, $username]);
        echo "<div class='success'>Admin user updated successfully!</div>";
    } else {
        // Create new admin user
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role, created_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt->execute([$username, $email, $hashed_password, $role]);
        echo "<div class='success'>Admin user created successfully!</div>";
    }
    
    echo "<div class='info'>";
    echo "<strong>Login Credentials:</strong><br>";
    echo "Username: <strong>" . $username . "</strong><br>";
    echo "Password: <strong>" . $password . "</strong><br>";
    echo "Email: " . $email . "<br>";
    echo "Role: " . $role . "<br>";
    echo "</div>";
    
    echo "<div class='warning'>";
    echo "<strong>IMPORTANT:</strong> Delete this file (create_admin.php) after use for security!";
    echo "</div>";
    
    echo "<div class='actions'>";
    echo "<a href='login.php' class='btn'>Go to Login Page</a>";
    echo "</div>";
    
} catch (PDOException $e) {
    echo "<div class='error'>Database Error: " . $e->getMessage() . "</div>";
    echo "<div class='info'>Make sure your database is set up correctly and the 'iticket' database exists.</div>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ITicket Admin Setup</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            padding: 20px; 
            max-width: 600px; 
            margin: 0 auto;
            background-color: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .success { 
            color: #155724;
            background: #d4edda; 
            padding: 15px; 
            border-radius: 5px; 
            margin: 15px 0;
            border: 1px solid #c3e6cb;
        }
        .error { 
            color: #721c24; 
            background: #f8d7da; 
            padding: 15px; 
            border-radius: 5px; 
            margin: 15px 0;
            border: 1px solid #f5c6cb;
        }
        .warning { 
            color: #856404; 
            background: #fff3cd; 
            padding: 15px; 
            border-radius: 5px; 
            margin: 15px 0;
            border: 1px solid #ffeaa7;
        }
        .info {
            color: #0c5460;
            background: #d1ecf1;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
            border: 1px solid #bee5eb;
        }
        .actions {
            text-align: center;
            margin-top: 20px;
        }
        .btn {
            background: #007bff;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 5px;
            display: inline-block;
            font-weight: bold;
        }
        .btn:hover {
            background: #0056b3;
        }
        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🎫 ITicket Admin Setup</h1>
    </div>
</body>
</html>