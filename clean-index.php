<?php
// Simple working index page
session_start();

// Database connection
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'booknest';

try {
    $conn = new mysqli($host, $user, $pass, $dbname);
    
    // Create database if not exists
    $conn->query("CREATE DATABASE IF NOT EXISTS $dbname");
    $conn->select_db($dbname);
    
    // Create simple tables
    $conn->query("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100),
        email VARCHAR(100),
        password VARCHAR(255),
        role VARCHAR(10) DEFAULT 'user'
    )");
    
    $conn->query("CREATE TABLE IF NOT EXISTS products (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(200),
        price DECIMAL(10,2),
        category VARCHAR(50),
        stock INT DEFAULT 10
    )");
    
    // Insert admin if not exists
    $admin_pass = password_hash('admin123', PASSWORD_DEFAULT);
    $conn->query("INSERT IGNORE INTO users (name, email, password, role) VALUES ('Admin', 'admin@booknest.com', '$admin_pass', 'admin')");
    
    // Insert sample products
    $conn->query("INSERT IGNORE INTO products (name, price, category) VALUES 
        ('The Great Gatsby', 15.99, 'Fiction'),
        ('1984', 12.99, 'Science Fiction'),
        ('To Kill a Mockingbird', 14.99, 'Fiction')");
    
} catch (Exception $e) {
    die("Database error: " . $e->getMessage());
}

// Handle login
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    $result = $conn->query("SELECT * FROM users WHERE email = '$email'");
    if ($result && $result->num_rows == 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['role'] = $user['role'];
        }
    }
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: index.php');
    exit();
}

function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function is_admin() {
    return isset($_SESSION['role']) && $_SESSION['role'] == 'admin';
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>BookNest - Online Bookstore</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f8f9fa; }
        .header { background: #2c3e50; color: white; padding: 1rem 0; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .container { max-width: 1200px; margin: 0 auto; padding: 0 20px; }
        .nav { display: flex; justify-content: space-between; align-items: center; }
        .logo { font-size: 1.5rem; font-weight: bold; }
        .nav-links a { color: white; text-decoration: none; margin-left: 20px; padding: 8px 16px; border-radius: 4px; transition: background 0.3s; }
        .nav-links a:hover { background: rgba(255,255,255,0.1); }
        .btn { background: #3498db; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; border: none; cursor: pointer; }
        .btn:hover { background: #2980b9; }
        .btn-success { background: #27ae60; }
        .btn-success:hover { background: #229954; }
        .hero { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 80px 0; text-align: center; }
        .hero h1 { font-size: 3rem; margin-bottom: 20px; }
        .hero p { font-size: 1.2rem; margin-bottom: 30px; opacity: 0.9; }
        .products { padding: 60px 0; }
        .products h2 { text-align: center; margin-bottom: 40px; font-size: 2rem; color: #2c3e50; }
        .product-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px; }
        .product { background: white; padding: 25px; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); text-align: center; }
        .product h3 { margin-bottom: 15px; color: #2c3e50; }
        .product .price { font-size: 1.5rem; color: #27ae60; font-weight: bold; margin-bottom: 15px; }
        .product .category { color: #7f8c8d; margin-bottom: 20px; }
        .login-form { max-width: 400px; margin: 50px auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 16px; }
        .alert { padding: 15px; margin-bottom: 20px; border-radius: 5px; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .footer { background: #2c3e50; color: white; padding: 40px 0; margin-top: 60px; text-align: center; }
    </style>
</head>
<body>
    <header class="header">
        <div class="container">
            <nav class="nav">
                <div class="logo">📚 BookNest</div>
                <div class="nav-links">
                    <a href="index.php">Home</a>
                    <a href="index.php?page=about">About</a>
                    <a href="index.php?page=contact">Contact</a>
                    <?php if (is_logged_in()): ?>
                        <span>Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?>!</span>
                        <?php if (is_admin()): ?>
                            <a href="admin/dashboard.php">Admin</a>
                        <?php endif; ?>
                        <a href="?logout=1">Logout</a>
                    <?php else: ?>
                        <a href="?page=login">Login</a>
                        <a href="?page=register">Register</a>
                    <?php endif; ?>
                </div>
            </nav>
        </div>
    </header>

    <?php if (isset($_GET['page']) && $_GET['page'] == 'login'): ?>
        <div class="container">
            <div class="login-form">
                <h2>Login to Your Account</h2>
                <form method="post">
                    <div class="form-group">
                        <label>Email:</label>
                        <input type="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label>Password:</label>
                        <input type="password" name="password" required>
                    </div>
                    <button type="submit" name="login" class="btn">Login</button>
                </form>
                <p style="margin-top: 20px; text-align: center; color: #666;">
                    Admin: admin@booknest.com / admin123
                </p>
            </div>
        </div>
    <?php else: ?>
        <section class="hero">
            <div class="container">
                <h1>Welcome to BookNest</h1>
                <p>Your trusted online bookstore for quality books</p>
                <?php if (is_logged_in()): ?>
                    <a href="#products" class="btn btn-success">Browse Books</a>
                <?php else: ?>
                    <a href="?page=login" class="btn btn-success">Start Shopping</a>
                <?php endif; ?>
            </div>
        </section>

        <section class="products" id="products">
            <div class="container">
                <h2>Featured Books</h2>
                <div class="product-grid">
                    <?php
                    $result = $conn->query("SELECT * FROM products ORDER BY id DESC LIMIT 6");
                    while ($product = $result->fetch_assoc()) {
                    ?>
                        <div class="product">
                            <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                            <div class="price">₹<?php echo number_format($product['price'], 2); ?></div>
                            <div class="category"><?php echo htmlspecialchars($product['category']); ?></div>
                            <div style="font-size: 3em; margin-bottom: 20px;">📚</div>
                            <?php if (is_logged_in()): ?>
                                <button class="btn">Add to Cart clean</button>
                            <?php else: ?>
                                <a href="?page=login" class="btn">Login to Buy</a>
                            <?php endif; ?>
                        </div>
                    <?php
                    }
                    ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2024 BookNest. All rights reserved.</p>
            <p>Your trusted online bookstore</p>
        </div>
    </footer>
</body>
</html>
