<?php
session_start();
require_once 'config/database.php';

// Check if user is admin or technician
if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] !== 'admin' && $_SESSION['user_role'] !== 'technician')) {
    header('Location: login.php');
    exit();
}

// Get statistics for display
try {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM tickets");
    $stmt->execute();
    $total_tickets = $stmt->fetchColumn();

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users");
    $stmt->execute();
    $total_users = $stmt->fetchColumn();

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM ticket_comments");
    $stmt->execute();
    $total_comments = $stmt->fetchColumn();
} catch (PDOException $e) {
    $total_tickets = $total_users = $total_comments = 0;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Export Data - ITicket</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>ITicket - Export Data</h1>
            <nav>
                <a href="index.php">Dashboard</a>
                <a href="create_ticket.php">New Ticket</a>
                <a href="admin.php">Admin Panel</a>
                <a href="logout.php">Logout</a>
            </nav>
        </header>

        <main>
            <div class="export-container">
                <h2>📊 Export System Data</h2>
                
                <div class="export-info">
                    <strong>ℹ️ Export Information:</strong><br>
                    • CSV files can be opened in Excel, Google Sheets, or any spreadsheet application<br>
                    • JSON files are suitable for data processing and backup purposes<br>
                    • All exports include complete data with timestamps and user information
                </div>

                <div class="export-options">
                    <div class="export-card">
                        <h4>🎫 Tickets Data</h4>
                        <p>Export all support tickets with details, status, and user information.</p>
                        <p><strong>Total Records:</strong> <?php echo $total_tickets; ?></p>
                        <div>
                            <a href="export.php?type=tickets&format=csv" class="btn btn-primary">Download CSV</a>
                            <a href="export.php?type=tickets&format=json" class="btn btn-secondary">Download JSON</a>
                        </div>
                    </div>

                    <?php if ($_SESSION['user_role'] === 'admin'): ?>
                    <div class="export-card">
                        <h4>👥 Users Data</h4>
                        <p>Export user accounts, roles, and registration information.</p>
                        <p><strong>Total Records:</strong> <?php echo $total_users; ?></p>
                        <div>
                            <a href="export.php?type=users&format=csv" class="btn btn-primary">Download CSV</a>
                            <a href="export.php?type=users&format=json" class="btn btn-secondary">Download JSON</a>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="export-card">
                        <h4>💬 Comments Data</h4>
                        <p>Export all ticket comments and communication history.</p>
                        <p><strong>Total Records:</strong> <?php echo $total_comments; ?></p>
                        <div>
                            <a href="export.php?type=comments&format=csv" class="btn btn-primary">Download CSV</a>
                            <a href="export.php?type=comments&format=json" class="btn btn-secondary">Download JSON</a>
                        </div>
                    </div>
                </div>

                <div class="export-info">
                    <strong>🔒 Privacy Notice:</strong><br>
                    Exported data contains sensitive information. Please handle according to your company's data protection policies.
                </div>

                <div style="text-align: center; margin-top: 2rem;">
                    <a href="admin.php" class="btn btn-success">← Back to Admin Panel</a>
                </div>
            </div>
        </main>
    </div>

    <script src="assets/js/main.js"></script>
</body>
</html>