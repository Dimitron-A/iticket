<?php
require_once 'config/database.php';

$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $new_password = $_POST['password'] ?? '';
    
    if ($username && $new_password) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        
        try {
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE username = ?");
            $result = $stmt->execute([$hashed_password, $username]);
            
            if ($stmt->rowCount() > 0) {
                $message = "Password updated successfully for user: " . htmlspecialchars($username) . "<br><strong>New Password:</strong> " . htmlspecialchars($new_password);
                $message_type = 'success';
            } else {
                $message = "User not found: " . htmlspecialchars($username);
                $message_type = 'error';
            }
        } catch (PDOException $e) {
            $message = "Database Error: " . $e->getMessage();
            $message_type = 'error';
        }
    } else {
        $message = "Please fill in all fields.";
        $message_type = 'error';
    }
}

// Show existing users
try {
    $stmt = $pdo->prepare("SELECT username, email, role FROM users ORDER BY role, username");
    $stmt->execute();
    $users = $stmt->fetchAll();
} catch (PDOException $e) {
    $users = [];
    $message = "Error fetching users: " . $e->getMessage();
    $message_type = 'error';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset - ITicket</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            padding: 20px; 
            max-width: 800px; 
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
        form { 
            background: #f8f9fa; 
            padding: 25px; 
            border-radius: 8px; 
            margin: 20px 0;
            border: 1px solid #dee2e6;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }
        input, select { 
            width: 100%; 
            padding: 10px; 
            margin: 5px 0 15px 0; 
            border: 1px solid #ddd; 
            border-radius: 5px;
            font-size: 14px;
            box-sizing: border-box;
        }
        button { 
            background: #007bff; 
            color: white; 
            padding: 12px 24px; 
            border: none; 
            border-radius: 5px; 
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
        }
        button:hover { 
            background: #0056b3; 
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin: 20px 0;
            background: white;
        }
        th, td { 
            padding: 12px; 
            text-align: left; 
            border-bottom: 1px solid #ddd; 
        }
        th { 
            background: #f8f9fa;
            font-weight: bold;
            color: #333;
        }
        tr:hover {
            background: #f8f9fa;
        }
        .role-admin { color: #dc3545; font-weight: bold; }
        .role-technician { color: #007bff; font-weight: bold; }
        .role-user { color: #28a745; font-weight: bold; }
        h1, h2 {
            color: #333;
        }
        h1 {
            text-align: center;
            margin-bottom: 10px;
        }
        .subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 30px;
        }
        .btn-link {
            display: inline-block;
            background: #28a745;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
        .btn-link:hover {
            background: #218838;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔐 ITicket Password Reset</h1>
        <p class="subtitle">Reset passwords for existing users</p>
        
        <div class="warning">
            <strong>⚠️ Security Warning:</strong> Delete this file after use for security!
        </div>
        
        <?php if ($message): ?>
            <div class="<?php echo $message_type; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <h2>Reset User Password</h2>
        <form method="POST">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required placeholder="Enter username from the table below">
            
            <label for="password">New Password:</label>
            <input type="password" id="password" name="password" required placeholder="Enter new password">
            
            <button type="submit">🔄 Reset Password</button>
        </form>
        
        <h2>Existing Users</h2>
        <?php if (empty($users)): ?>
            <div class="error">No users found. Make sure your database is set up correctly.</div>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($user['username']); ?></strong></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td class="role-<?php echo $user['role']; ?>">
                                <?php echo ucfirst(htmlspecialchars($user['role'])); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
        
        <div style="text-align: center;">
            <a href="login.php" class="btn-link">🚀 Go to Login Page</a>
        </div>
    </div>
</body>
</html>