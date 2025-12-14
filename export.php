<?php
session_start();
require_once 'config/database.php';

// Check if user is admin or technician
if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] !== 'admin' && $_SESSION['user_role'] !== 'technician')) {
    header('Location: login.php');
    exit();
}

$export_type = $_GET['type'] ?? 'tickets';
$format = $_GET['format'] ?? 'csv';

// Set headers for file download
if ($format === 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="iticket_' . $export_type . '_' . date('Y-m-d') . '.csv"');
} else {
    header('Content-Type: application/json');
    header('Content-Disposition: attachment; filename="iticket_' . $export_type . '_' . date('Y-m-d') . '.json"');
}

try {
    if ($export_type === 'tickets') {
        // Export tickets with user information
        $stmt = $pdo->prepare("
            SELECT 
                t.id,
                t.title,
                t.description,
                t.category,
                t.priority,
                t.status,
                t.created_at,
                t.updated_at,
                u.username,
                u.email
            FROM tickets t 
            JOIN users u ON t.user_id = u.id 
            ORDER BY t.created_at DESC
        ");
        $stmt->execute();
        $data = $stmt->fetchAll();
        
        if ($format === 'csv') {
            // Output CSV headers
            echo "ID,Title,Description,Category,Priority,Status,Created Date,Updated Date,Username,Email\n";
            
            // Output data rows
            foreach ($data as $row) {
                echo '"' . $row['id'] . '",';
                echo '"' . str_replace('"', '""', $row['title']) . '",';
                echo '"' . str_replace('"', '""', $row['description']) . '",';
                echo '"' . $row['category'] . '",';
                echo '"' . $row['priority'] . '",';
                echo '"' . $row['status'] . '",';
                echo '"' . $row['created_at'] . '",';
                echo '"' . ($row['updated_at'] ?? '') . '",';
                echo '"' . $row['username'] . '",';
                echo '"' . $row['email'] . '"' . "\n";
            }
        } else {
            // Output JSON
            echo json_encode($data, JSON_PRETTY_PRINT);
        }
        
    } elseif ($export_type === 'users') {
        // Export users (admin only)
        if ($_SESSION['user_role'] !== 'admin') {
            die('Access denied');
        }
        
        $stmt = $pdo->prepare("
            SELECT 
                id,
                username,
                email,
                role,
                created_at
            FROM users 
            ORDER BY created_at DESC
        ");
        $stmt->execute();
        $data = $stmt->fetchAll();
        
        if ($format === 'csv') {
            // Output CSV headers
            echo "ID,Username,Email,Role,Created Date\n";
            
            // Output data rows
            foreach ($data as $row) {
                echo '"' . $row['id'] . '",';
                echo '"' . $row['username'] . '",';
                echo '"' . $row['email'] . '",';
                echo '"' . $row['role'] . '",';
                echo '"' . $row['created_at'] . '"' . "\n";
            }
        } else {
            // Output JSON
            echo json_encode($data, JSON_PRETTY_PRINT);
        }
        
    } elseif ($export_type === 'comments') {
        // Export ticket comments
        $stmt = $pdo->prepare("
            SELECT 
                tc.id,
                tc.ticket_id,
                tc.comment,
                tc.created_at,
                u.username,
                t.title as ticket_title
            FROM ticket_comments tc
            JOIN users u ON tc.user_id = u.id
            JOIN tickets t ON tc.ticket_id = t.id
            ORDER BY tc.created_at DESC
        ");
        $stmt->execute();
        $data = $stmt->fetchAll();
        
        if ($format === 'csv') {
            // Output CSV headers
            echo "ID,Ticket ID,Ticket Title,Comment,Username,Created Date\n";
            
            // Output data rows
            foreach ($data as $row) {
                echo '"' . $row['id'] . '",';
                echo '"' . $row['ticket_id'] . '",';
                echo '"' . str_replace('"', '""', $row['ticket_title']) . '",';
                echo '"' . str_replace('"', '""', $row['comment']) . '",';
                echo '"' . $row['username'] . '",';
                echo '"' . $row['created_at'] . '"' . "\n";
            }
        } else {
            // Output JSON
            echo json_encode($data, JSON_PRETTY_PRINT);
        }
        
    } else {
        die('Invalid export type');
    }
    
} catch (PDOException $e) {
    die('Database error: ' . $e->getMessage());
}
?>