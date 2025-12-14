<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_role = $_SESSION['user_role'];
$tickets = getTickets($pdo, $user_role, $_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ITicket - IT Support System</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>ITicket - IT Support System</h1>
            <nav>
                <a href="index.php">Dashboard</a>
                <a href="create_ticket.php">New Ticket</a>
                <?php if ($user_role === 'admin' || $user_role === 'technician'): ?>
                    <a href="admin.php">Admin Panel</a>
                <?php endif; ?>
                <a href="logout.php">Logout</a>
            </nav>
        </header>

        <main>
            <div class="dashboard">
                <h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
                
                <div class="stats">
                    <div class="stat-card">
                        <h3>Open Tickets</h3>
                        <span class="stat-number"><?php echo countTicketsByStatus($pdo, 'open'); ?></span>
                    </div>
                    <div class="stat-card">
                        <h3>In Progress</h3>
                        <span class="stat-number"><?php echo countTicketsByStatus($pdo, 'in_progress'); ?></span>
                    </div>
                    <div class="stat-card">
                        <h3>Resolved</h3>
                        <span class="stat-number"><?php echo countTicketsByStatus($pdo, 'resolved'); ?></span>
                    </div>
                </div>

                <div class="tickets-section">
                    <h3>Recent Tickets</h3>
                    <div class="tickets-list">
                        <?php foreach ($tickets as $ticket): ?>
                            <div class="ticket-item" data-priority="<?php echo $ticket['priority']; ?>">
                                <div class="ticket-header">
                                    <span class="ticket-id">#<?php echo $ticket['id']; ?></span>
                                    <span class="ticket-status status-<?php echo $ticket['status']; ?>">
                                        <?php echo ucfirst(str_replace('_', ' ', $ticket['status'])); ?>
                                    </span>
                                    <span class="ticket-priority priority-<?php echo $ticket['priority']; ?>">
                                        <?php echo ucfirst($ticket['priority']); ?>
                                    </span>
                                </div>
                                <h4><?php echo htmlspecialchars($ticket['title']); ?></h4>
                                <p><?php echo htmlspecialchars(substr($ticket['description'], 0, 100)) . '...'; ?></p>
                                <div class="ticket-meta">
                                    <span>Created: <?php echo date('M j, Y', strtotime($ticket['created_at'])); ?></span>
                                    <span>Category: <?php echo htmlspecialchars($ticket['category']); ?></span>
                                </div>
                                <div class="ticket-actions">
                                    <a href="view_ticket.php?id=<?php echo $ticket['id']; ?>" class="btn btn-primary">View</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="assets/js/main.js"></script>
</body>
</html>