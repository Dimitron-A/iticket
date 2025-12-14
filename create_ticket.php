<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $category = $_POST['category'] ?? '';
    $priority = $_POST['priority'] ?? '';
    
    if ($title && $description && $category && $priority) {
        if (createTicket($pdo, $_SESSION['user_id'], $title, $description, $category, $priority)) {
            $success = 'Ticket created successfully!';
        } else {
            $error = 'Error creating ticket. Please try again.';
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
    <title>Create Ticket - ITicket</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>ITicket - IT Support System</h1>
            <nav>
                <a href="index.php">Dashboard</a>
                <a href="create_ticket.php">New Ticket</a>
                <?php if ($_SESSION['user_role'] === 'admin' || $_SESSION['user_role'] === 'technician'): ?>
                    <a href="admin.php">Admin Panel</a>
                <?php endif; ?>
                <a href="logout.php">Logout</a>
            </nav>
        </header>

        <main>
            <div class="form-container">
                <h2>Create New Ticket</h2>
                
                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                <?php endif; ?>
                
                <?php if ($error): ?>
                    <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                
                <form method="POST">
                    <div class="form-group">
                        <label for="title">Title:</label>
                        <input type="text" id="title" name="title" required 
                               placeholder="Brief description of the issue">
                    </div>
                    
                    <div class="form-group">
                        <label for="category">Category:</label>
                        <select id="category" name="category" required>
                            <option value="">Select Category</option>
                            <option value="hardware">Hardware</option>
                            <option value="software">Software</option>
                            <option value="network">Network</option>
                            <option value="email">Email</option>
                            <option value="printer">Printer</option>
                            <option value="phone">Phone System</option>
                            <option value="security">Security</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="priority">Priority:</label>
                        <select id="priority" name="priority" required>
                            <option value="">Select Priority</option>
                            <option value="low">Low - Can wait</option>
                            <option value="medium">Medium - Normal priority</option>
                            <option value="high">High - Urgent</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Description:</label>
                        <textarea id="description" name="description" required 
                                  placeholder="Please provide detailed information about the issue, including steps to reproduce, error messages, and any troubleshooting you've already tried."></textarea>
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Create Ticket</button>
                        <a href="index.php" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <script src="assets/js/main.js"></script>
</body>
</html>