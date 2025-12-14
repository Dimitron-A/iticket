<?php
// ITicket Database Configuration Template
// Copy this file to database.php and update with your actual database credentials

$host = 'localhost';
$dbname = 'iticket';
$username = 'root';          // Your database username
$password = '';              // Your database password (usually empty for XAMPP)

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>