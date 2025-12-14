<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$ticket_id = $_GET['id'] ?? 0;
$ticket = getTicketById($pdo, $ticket_id);

if (!$ticket) {
    header('Location: index.php');
    exit();
}

// Check if user can view this ticket
if ($_SESSION['user_role'] !== 'admin' && $_SESSION['user_role'] !== 'technician' && $ticket['user_id'] != $_SESSION['user_id']) {
    header('Location: index.php');
    exit();
}

$comments = getTicketComments($pdo, $ticket_id);
$success = '';
$error = '';

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $new_status = $_POST['status'] ?? '';
    if ($new_status && ($_SESSION['user_role'] === 'admin' || $_SESSION['user_role'] === 'technician')) {
        if (updateTicketStatus($pdo, $ticket_id, $new_status)) {
            $success = 'Ticket status updated successfully!';
            $ticket['status'] = $new_status;
        } else {
            $error = 'Error updating ticket status.';
        }
    }
}

// Handle comment addition
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_comment'])) {
    $comment = $_POST['comment'] ?? '';
    if ($comment) {
        if (addTicketComment($pdo, $ticket_id, $_SESSION['user_id'], $comment)) {
            $success = 'Comment added successfully!';
            $comments = getTicketComments($pdo, $ticket_id); // Refresh comments
        } else {
            $error = 'Error adding comment.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket #<?php echo $ticket['id']; ?> - ITicket</title>
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
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <div class="ticket-details">
                <div class="ticket-header">
                    <h2>Ticket #<?php echo $ticket['id']; ?>: <?php echo htmlspecialchars($ticket['title']); ?></h2>
                    <div class="ticket-badges">
                        <span class="ticket-status status-<?php echo $ticket['status']; ?>">
                            <?php echo ucfirst(str_replace('_', ' ', $ticket['status'])); ?>
                        </span>
                        <span class="ticket-priority priority-<?php echo $ticket['priority']; ?>">
                            <?php echo ucfirst($ticket['priority']); ?>
                        </span>
                    </div>
                </div>

                <div class="ticket-info">
                    <div class="info-grid">
                        <div class="info-item">
                            <strong>Submitted by:</strong> <?php echo htmlspecialchars($ticket['username']); ?>
                        </div>
                        <div class="info-item">
                            <strong>Email:</strong> <?php echo htmlspecialchars($ticket['email']); ?>
                        </div>
                        <div class="info-item">
                            <strong>Category:</strong> <?php echo htmlspecialchars($ticket['category']); ?>
                        </div>
                        <div class="info-item">
                            <strong>Created:</strong> <?php echo date('M j, Y g:i A', strtotime($ticket['created_at'])); ?>
                        </div>
                        <?php if ($ticket['updated_at']): ?>
                        <div class="info-item">
                            <strong>Last Updated:</strong> <?php echo date('M j, Y g:i A', strtotime($ticket['updated_at'])); ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="ticket-description">
                    <h3>Description</h3>
                    <p><?php echo nl2br(htmlspecialchars($ticket['description'])); ?></p>
                </div>

                <?php if ($_SESSION['user_role'] === 'admin' || $_SESSION['user_role'] === 'technician'): ?>
                <div class="status-update">
                    <h3>Update Status</h3>
                    <form method="POST" style="display: inline-block;">
                        <select name="status" class="status-select">
                            <option value="open" <?php echo $ticket['status'] === 'open' ? 'selected' : ''; ?>>Open</option>
                            <option value="in_progress" <?php echo $ticket['status'] === 'in_progress' ? 'selected' : ''; ?>>In Progress</option>
                            <option value="resolved" <?php echo $ticket['status'] === 'resolved' ? 'selected' : ''; ?>>Resolved</option>
                            <option value="closed" <?php echo $ticket['status'] === 'closed' ? 'selected' : ''; ?>>Closed</option>
                        </select>
                        <button type="submit" name="update_status" class="btn btn-primary">Update</button>
                    </form>
                </div>
                <?php endif; ?>

                <div class="comments-section">
                    <h3>Comments</h3>
                    
                    <div class="comments-list">
                        <?php foreach ($comments as $comment): ?>
                            <div class="comment">
                                <div class="comment-header">
                                    <strong><?php echo htmlspecialchars($comment['username']); ?></strong>
                                    <span class="comment-date"><?php echo date('M j, Y g:i A', strtotime($comment['created_at'])); ?></span>
                                </div>
                                <div class="comment-body">
                                    <?php echo nl2br(htmlspecialchars($comment['comment'])); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="add-comment">
                        <h4>Add Comment</h4>
                        <form method="POST">
                            <div class="form-group">
                                <textarea name="comment" placeholder="Enter your comment..." required></textarea>
                            </div>
                            <button type="submit" name="add_comment" class="btn btn-primary">Add Comment</button>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="assets/js/main.js"></script>
</body>
</html>