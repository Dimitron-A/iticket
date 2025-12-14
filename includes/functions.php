<?php
function getTickets($pdo, $user_role, $user_id) {
    if ($user_role === 'admin' || $user_role === 'technician') {
        $stmt = $pdo->prepare("SELECT * FROM tickets ORDER BY created_at DESC LIMIT 10");
        $stmt->execute();
    } else {
        $stmt = $pdo->prepare("SELECT * FROM tickets WHERE user_id = ? ORDER BY created_at DESC LIMIT 10");
        $stmt->execute([$user_id]);
    }
    return $stmt->fetchAll();
}

function countTicketsByStatus($pdo, $status) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM tickets WHERE status = ?");
    $stmt->execute([$status]);
    return $stmt->fetchColumn();
}

function createTicket($pdo, $user_id, $title, $description, $category, $priority) {
    $stmt = $pdo->prepare("INSERT INTO tickets (user_id, title, description, category, priority, status, created_at) VALUES (?, ?, ?, ?, ?, 'open', NOW())");
    return $stmt->execute([$user_id, $title, $description, $category, $priority]);
}

function getTicketById($pdo, $id) {
    $stmt = $pdo->prepare("SELECT t.*, u.username, u.email FROM tickets t JOIN users u ON t.user_id = u.id WHERE t.id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function updateTicketStatus($pdo, $id, $status) {
    $stmt = $pdo->prepare("UPDATE tickets SET status = ?, updated_at = NOW() WHERE id = ?");
    return $stmt->execute([$status, $id]);
}

function addTicketComment($pdo, $ticket_id, $user_id, $comment) {
    $stmt = $pdo->prepare("INSERT INTO ticket_comments (ticket_id, user_id, comment, created_at) VALUES (?, ?, ?, NOW())");
    return $stmt->execute([$ticket_id, $user_id, $comment]);
}

function getTicketComments($pdo, $ticket_id) {
    $stmt = $pdo->prepare("SELECT tc.*, u.username FROM ticket_comments tc JOIN users u ON tc.user_id = u.id WHERE tc.ticket_id = ? ORDER BY tc.created_at ASC");
    $stmt->execute([$ticket_id]);
    return $stmt->fetchAll();
}

function authenticateUser($pdo, $username, $password) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password'])) {
        return $user;
    }
    return false;
}

function registerUser($pdo, $username, $email, $password, $role = 'user') {
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role, created_at) VALUES (?, ?, ?, ?, NOW())");
    return $stmt->execute([$username, $email, $hashed_password, $role]);
}
?>