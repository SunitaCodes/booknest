<?php
// Minimal database configuration - no sessions, no complex setup
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database configuration
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'booknest';

// Create connection
try {
    $conn = new mysqli($host, $username, $password);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    // Create database if not exists
    $conn->query("CREATE DATABASE IF NOT EXISTS $database");
    $conn->select_db($database);
    
    // Create tables if not exists
    $conn->query("CREATE TABLE IF NOT EXISTS users (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        full_name VARCHAR(100) NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        phone VARCHAR(10) NOT NULL,
        address TEXT NOT NULL,
        password VARCHAR(255) NOT NULL,
        role ENUM('user', 'admin') DEFAULT 'user',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    
    // Create admin user if not exists
    $admin_email = 'admin@booknest.com';
    $admin_password = password_hash('admin123', PASSWORD_DEFAULT);
    
    $conn->query("INSERT IGNORE INTO users (full_name, email, phone, address, password, role) 
                  VALUES ('Admin User', '$admin_email', '9876543210', 'Admin Office', '$admin_password', 'admin')");
    
    echo "✅ Minimal database setup successful<br>";
    
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "<br>";
}

// Start session
session_start();

echo "✅ Session started<br>";

// Basic helper functions
function clean_input($data) {
    global $conn;
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $conn->real_escape_string($data);
}

function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function is_admin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'admin';
}

echo "✅ Basic functions defined<br>";
echo "✅ Minimal config loaded successfully<br>";
?>
