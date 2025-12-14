<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] !== 'admin' && $_SESSION['user_role'] !== 'technician')) {
    header('Location: index.php');
    exit();
}

// Get all tickets for admin view
$stmt = $pdo->prepare("SELECT t.*, u.username, u.email FROM tickets t JOIN users u ON t.user_id = u.id ORDER BY t.created_at DESC");
$stmt->execute();
$all_tickets = $stmt->fetchAll();

// Get user statistics
$stmt = $pdo->prepare("SELECT COUNT(*) as total_users FROM users WHERE role = 'user'");
$stmt->execute();
$total_users = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) as total_tickets FROM tickets");
$stmt->execute();
$total_tickets = $stmt->fetchColumn();

// Get all users for management
$stmt = $pdo->prepare("SELECT id, username, email, role, created_at FROM users ORDER BY created_at DESC");
$stmt->execute();
$all_users = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - ITicket</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>ITicket - Admin Panel</h1>
            <nav>
                <a href="index.php">Dashboard</a>
                <a href="create_ticket.php">New Ticket</a>
                <a href="admin.php">Admin Panel</a>
                <a href="logout.php">Logout</a>
            </nav>
        </header>

        <main>
            <div class="admin-dashboard">
                <h2>System Overview</h2>
                
                <div class="stats">
                    <div class="stat-card">
                        <h3>Total Users</h3>
                        <span class="stat-number"><?php echo $total_users; ?></span>
                    </div>
                    <div class="stat-card">
                        <h3>Total Tickets</h3>
                        <span class="stat-number"><?php echo $total_tickets; ?></span>
                    </div>
                    <div class="stat-card">
                        <h3>Open Tickets</h3>
                        <span class="stat-number"><?php echo countTicketsByStatus($pdo, 'open'); ?></span>
                    </div>
                    <div class="stat-card">
                        <h3>In Progress</h3>
                        <span class="stat-number"><?php echo countTicketsByStatus($pdo, 'in_progress'); ?></span>
                    </div>
                </div>

                <div class="admin-controls">
                    <h3>Quick Actions</h3>
                    <div class="action-buttons">
                        <a href="register.php" class="btn btn-success">Create New User</a>
                        <button class="btn btn-primary" onclick="filterTickets('all')">All Tickets</button>
                        <button class="btn btn-warning" onclick="filterTickets('open')">Open Only</button>
                        <button class="btn btn-success" onclick="filterTickets('high')">High Priority</button>
                        <div class="dropdown">
                            <button class="btn btn-secondary dropdown-toggle" onclick="toggleDropdown()">Export Data ▼</button>
                            <div class="dropdown-menu" id="exportDropdown">
                                <a href="export_page.php" class="dropdown-item">📋 Export Center</a>
                                <hr style="margin: 5px 0;">
                                <a href="export.php?type=tickets&format=csv" class="dropdown-item">📊 Tickets (CSV)</a>
                                <a href="export.php?type=tickets&format=json" class="dropdown-item">📊 Tickets (JSON)</a>
                                <?php if ($_SESSION['user_role'] === 'admin'): ?>
                                    <a href="export.php?type=users&format=csv" class="dropdown-item">👥 Users (CSV)</a>
                                    <a href="export.php?type=users&format=json" class="dropdown-item">👥 Users (JSON)</a>
                                <?php endif; ?>
                                <a href="export.php?type=comments&format=csv" class="dropdown-item">💬 Comments (CSV)</a>
                                <a href="export.php?type=comments&format=json" class="dropdown-item">💬 Comments (JSON)</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="search-section">
                    <input type="text" id="ticket-search" placeholder="Search tickets..." class="search-input">
                </div>

                <div class="users-section">
                    <h3>User Management</h3>
                    <div class="users-table">
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($all_users as $user): ?>
                                    <tr>
                                        <td><?php echo $user['id']; ?></td>
                                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                                        <td>
                                            <span class="role-badge role-<?php echo $user['role']; ?>">
                                                <?php echo ucfirst($user['role']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('M j, Y', strtotime($user['created_at'])); ?></td>
                                        <td>
                                            <button class="btn btn-warning btn-sm" onclick="editUser(<?php echo $user['id']; ?>)">Edit</button>
                                            <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                                <button class="btn btn-danger btn-sm" onclick="deleteUser(<?php echo $user['id']; ?>)">Delete</button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="tickets-section">
                    <h3>All Tickets Management</h3>
                    <div class="tickets-table">
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Title</th>
                                    <th>User</th>
                                    <th>Category</th>
                                    <th>Priority</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($all_tickets as $ticket): ?>
                                    <tr class="ticket-row" data-status="<?php echo $ticket['status']; ?>" data-priority="<?php echo $ticket['priority']; ?>">
                                        <td>#<?php echo $ticket['id']; ?></td>
                                        <td><?php echo htmlspecialchars(substr($ticket['title'], 0, 50)) . '...'; ?></td>
                                        <td><?php echo htmlspecialchars($ticket['username']); ?></td>
                                        <td><?php echo htmlspecialchars($ticket['category']); ?></td>
                                        <td>
                                            <span class="priority-badge priority-<?php echo $ticket['priority']; ?>">
                                                <?php echo ucfirst($ticket['priority']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <select class="status-select" data-ticket-id="<?php echo $ticket['id']; ?>">
                                                <option value="open" <?php echo $ticket['status'] === 'open' ? 'selected' : ''; ?>>Open</option>
                                                <option value="in_progress" <?php echo $ticket['status'] === 'in_progress' ? 'selected' : ''; ?>>In Progress</option>
                                                <option value="resolved" <?php echo $ticket['status'] === 'resolved' ? 'selected' : ''; ?>>Resolved</option>
                                                <option value="closed" <?php echo $ticket['status'] === 'closed' ? 'selected' : ''; ?>>Closed</option>
                                            </select>
                                        </td>
                                        <td><?php echo date('M j, Y', strtotime($ticket['created_at'])); ?></td>
                                        <td>
                                            <a href="view_ticket.php?id=<?php echo $ticket['id']; ?>" class="btn btn-primary btn-sm">View</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="assets/js/main.js"></script>
    <script>
        function filterTickets(filter) {
            const rows = document.querySelectorAll('.ticket-row');
            rows.forEach(row => {
                if (filter === 'all') {
                    row.style.display = 'table-row';
                } else if (filter === 'open') {
                    row.style.display = row.dataset.status === 'open' ? 'table-row' : 'none';
                } else if (filter === 'high') {
                    row.style.display = row.dataset.priority === 'high' ? 'table-row' : 'none';
                }
            });
        }

        function exportTickets() {
            window.location.href = 'export.php?type=tickets&format=csv';
        }

        function toggleDropdown() {
            const dropdown = document.getElementById('exportDropdown');
            dropdown.classList.toggle('show');
        }

        // Close dropdown when clicking outside
        window.onclick = function(event) {
            if (!event.target.matches('.dropdown-toggle')) {
                const dropdowns = document.getElementsByClassName('dropdown-menu');
                for (let i = 0; i < dropdowns.length; i++) {
                    const openDropdown = dropdowns[i];
                    if (openDropdown.classList.contains('show')) {
                        openDropdown.classList.remove('show');
                    }
                }
            }
        }
    </script>
</body>
</html>