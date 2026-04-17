<?php
// Start session before any output
session_start();

// Database configuration
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'booknest';

// Create connection
$conn = new mysqli($host, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database if not exists
$sql = "CREATE DATABASE IF NOT EXISTS $database";
if ($conn->query($sql) === TRUE) {
    // Select the database
    $conn->select_db($database);
    
    // Create tables if not exists
    
    // Users table
    $sql = "CREATE TABLE IF NOT EXISTS users (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        full_name VARCHAR(100) NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        phone VARCHAR(10) NOT NULL,
        address TEXT NOT NULL,
        password VARCHAR(255) NOT NULL,
        role ENUM('user', 'admin') DEFAULT 'user',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $conn->query($sql);
    
    // Products table
    $sql = "CREATE TABLE IF NOT EXISTS products (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(200) NOT NULL,
        category VARCHAR(50) NOT NULL,
        price DECIMAL(10,2) NOT NULL,
        stock INT(11) NOT NULL DEFAULT 0,
        image VARCHAR(255) DEFAULT 'default-book.jpg',
        description TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $conn->query($sql);
    
    // Cart table
    $sql = "CREATE TABLE IF NOT EXISTS cart (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        user_id INT(11) NOT NULL,
        product_id INT(11) NOT NULL,
        quantity INT(11) NOT NULL DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
        UNIQUE KEY unique_cart (user_id, product_id)
    )";
    $conn->query($sql);
    
    // Orders table
    $sql = "CREATE TABLE IF NOT EXISTS orders (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        user_id INT(11) NOT NULL,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL,
        phone VARCHAR(10) NOT NULL,
        address TEXT NOT NULL,
        payment_method ENUM('cash_on_delivery', 'esewa') NOT NULL,
        total_price DECIMAL(10,2) NOT NULL,
        status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )";
    $conn->query($sql);
    
    // Order items table
    $sql = "CREATE TABLE IF NOT EXISTS order_items (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        order_id INT(11) NOT NULL,
        product_id INT(11) NOT NULL,
        quantity INT(11) NOT NULL,
        price DECIMAL(10,2) NOT NULL,
        FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
    )";
    $conn->query($sql);
    
    // Insert sample admin user if not exists
    $admin_email = 'admin@booknest.com';
    $check_admin = "SELECT id FROM users WHERE email = '$admin_email'";
    $result = $conn->query($check_admin);
    
    if ($result->num_rows == 0) {
        $admin_password = password_hash('admin123', PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (full_name, email, phone, address, password, role) VALUES 
                ('Admin User', '$admin_email', '9876543210', 'Admin Office', '$admin_password', 'admin')";
        $conn->query($sql);
    }
    
    // Insert sample products if none exist
    $check_products = "SELECT COUNT(*) as count FROM products";
    $result = $conn->query($check_products);
    $row = $result->fetch_assoc();
    
    if ($row['count'] == 0) {
        $sample_products = [
            ["The Great Gatsby", "Fiction", 15.99, 50, "gatsby.jpg", "A classic American novel"],
            ["To Kill a Mockingbird", "Fiction", 18.99, 30, "mockingbird.jpg", "A gripping tale of racial injustice"],
            ["1984", "Science Fiction", 16.99, 40, "1984.jpg", "A dystopian social science fiction novel"],
            ["Pride and Prejudice", "Romance", 14.99, 35, "pride.jpg", "A romantic novel of manners"],
            ["The Catcher in the Rye", "Fiction", 17.99, 25, "catcher.jpg", "A story about teenage rebellion"],
            ["Harry Potter and the Sorcerer's Stone", "Fantasy", 19.99, 60, "harry.jpg", "The first book in the Harry Potter series"],
            ["The Da Vinci Code", "Mystery", 20.99, 45, "davinci.jpg", "A mystery thriller novel"],
            ["Sapiens", "Education", 22.99, 55, "sapiens.jpg", "A brief history of humankind"],
            ["The Alchemist", "Philosophy", 13.99, 70, "alchemist.jpg", "A philosophical book"],
            ["Atomic Habits", "Self Help", 21.99, 65, "atomic.jpg", "An easy & proven way to build good habits"]
        ];
        
        foreach ($sample_products as $product) {
            $sql = "INSERT INTO products (name, category, price, stock, image, description) VALUES 
                    ('$product[0]', '$product[1]', $product[2], $product[3], '$product[4]', '$product[5]')";
            $conn->query($sql);
        }
    }
    
} else {
    die("Error creating database: " . $conn->error);
}

// Helper functions
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

function redirect($url) {
    header("Location: $url");
    exit();
}

function display_message() {
    if (isset($_SESSION['message'])) {
        echo '<div class="alert alert-' . $_SESSION['message_type'] . '">' . $_SESSION['message'] . '</div>';
        unset($_SESSION['message']);
        unset($_SESSION['message_type']);
    }
}
?>
