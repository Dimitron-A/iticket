-- ITicket Database Schema
-- Create database
CREATE DATABASE IF NOT EXISTS iticket;
USE iticket;

-- Users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'technician', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tickets table
CREATE TABLE tickets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    category ENUM('hardware', 'software', 'network', 'email', 'printer', 'phone', 'security', 'other') NOT NULL,
    priority ENUM('low', 'medium', 'high') NOT NULL,
    status ENUM('open', 'in_progress', 'resolved', 'closed') DEFAULT 'open',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Ticket comments table
CREATE TABLE ticket_comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ticket_id INT NOT NULL,
    user_id INT NOT NULL,
    comment TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ticket_id) REFERENCES tickets(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Insert default admin user (password: admin123)
INSERT INTO users (username, email, password, role) VALUES 
('admin', 'admin@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Insert sample technician (password: tech123)
INSERT INTO users (username, email, password, role) VALUES 
('technician', 'tech@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'technician');

-- Insert sample user (password: user123)
INSERT INTO users (username, email, password, role) VALUES 
('user1', 'user1@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user');

-- Insert sample tickets
INSERT INTO tickets (user_id, title, description, category, priority, status) VALUES 
(3, 'Computer won\'t start', 'My computer is not turning on when I press the power button. The power LED is not lighting up.', 'hardware', 'high', 'open'),
(3, 'Email not working', 'I cannot send or receive emails. Getting error message about server connection.', 'email', 'medium', 'in_progress'),
(3, 'Printer offline', 'The office printer shows as offline and won\'t print documents.', 'printer', 'low', 'resolved');

-- Insert sample comments
INSERT INTO ticket_comments (ticket_id, user_id, comment) VALUES 
(2, 2, 'I\'ve checked the email server settings. Please try restarting Outlook and let me know if the issue persists.'),
(2, 3, 'I restarted Outlook but still getting the same error. The error code is 0x800CCC0E.'),
(3, 2, 'Printer has been reset and is now online. Please test printing a document.');

-- Create indexes for better performance
CREATE INDEX idx_tickets_user_id ON tickets(user_id);
CREATE INDEX idx_tickets_status ON tickets(status);
CREATE INDEX idx_tickets_created_at ON tickets(created_at);
CREATE INDEX idx_comments_ticket_id ON ticket_comments(ticket_id);