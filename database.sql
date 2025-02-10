CREATE DATABASE IF NOT EXISTS user_management;
USE user_management;

-- Create combined users table
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    first_name VARCHAR(50),
    last_name VARCHAR(50),
    phone VARCHAR(20),
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default admin user (password: admin123)
INSERT INTO users (username, email, password, role, first_name, last_name) VALUES 
('admin', 'admin@example.com', '$2y$10$GWxh6L.IOsjK7Rl748LKoOeniA2b557GDUTHbKePqOE1/EduO44c6', 'admin', 'System', 'Admin');

-- Insert default test user (password: User123!)
INSERT INTO users (username, email, password, role, first_name, last_name) VALUES 
('user', 'user@example.com', '$2y$10$EZCMIFsAKsNHQZsTbV/FdujfbA/W3hbxbjKTu8fkJ94IIMyVy1FAa', 'user', 'Test', 'User');
