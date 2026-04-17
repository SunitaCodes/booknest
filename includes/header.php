<?php
// Smart path detection - works from any directory
$current_dir = dirname($_SERVER['PHP_SELF']);

if (strpos($current_dir, '/admin') !== false || strpos($current_dir, '/user') !== false || strpos($current_dir, '/auth') !== false) {
    // We're in a subdirectory
    require_once '../config/db.php';
} else {
    // We're in the root directory
    require_once 'config/db.php';
}

// Get cart count if user is logged in
$cart_count = 0;
if (is_logged_in()) {
    $user_id = $_SESSION['user_id'];
    $cart_query = "SELECT SUM(quantity) as total FROM cart WHERE user_id = $user_id";
    $cart_result = $conn->query($cart_query);
    if ($cart_result && $cart_result->num_rows > 0) {
        $cart_row = $cart_result->fetch_assoc();
        $cart_count = $cart_row['total'];
    }
}

// Get categories for navigation
$categories_query = "SELECT DISTINCT category FROM products ORDER BY category";
$categories_result = $conn->query($categories_query);
$categories = [];
if ($categories_result) {
    while ($row = $categories_result->fetch_assoc()) {
        $categories[] = $row['category'];
    }
}

// Function to get correct base path
function get_base_path() {
    $current_dir = dirname($_SERVER['PHP_SELF']);
    if (strpos($current_dir, '/admin') !== false) {
        return '../';
    } elseif (strpos($current_dir, '/user') !== false) {
        return '../';
    } elseif (strpos($current_dir, '/auth') !== false) {
        return '../';
    } else {
        return '';
    }
}

$base_path = get_base_path();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?>BookNest</title>
    <link rel="stylesheet" href="<?php echo $base_path; ?>assets/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <header class="header">
        <nav class="navbar">
            <div class="nav-container">
                <div class="nav-logo">
                    <a href="<?php echo $base_path; ?>index.php">
                        <h1>📚 BookNest</h1>
                    </a>
                </div>

                <div class="nav-menu">
                    <ul class="nav-links">
                        <li><a href="<?php echo $base_path; ?>index.php" class="nav-link">Home</a></li>
                        <li class="nav-dropdown">
                            <a href="#" class="nav-link">Categories ▼</a>
                            <ul class="dropdown-menu">
                                <?php foreach ($categories as $category): ?>
                                    <li><a href="<?php echo $base_path; ?>index.php?category=<?php echo urlencode($category); ?>"><?php echo htmlspecialchars($category); ?></a></li>
                                <?php endforeach; ?>
                            </ul>
                        </li>
                        <li><a href="<?php echo $base_path; ?>about.php" class="nav-link">About</a></li>
                        <li><a href="<?php echo $base_path; ?>contact.php" class="nav-link">Contact</a></li>
                        <?php if (is_logged_in() && is_admin()): ?>
                            <li><a href="<?php echo $base_path; ?>admin/dashboard.php" class="nav-link admin-nav">🔧 Admin</a></li>
                        <?php endif; ?>
                    </ul>
                </div>

                <div class="nav-actions">
                    <div class="search-bar">
                        <form action="<?php echo $base_path; ?>index.php" method="GET">
                            <input type="text" name="search" placeholder="Search books..." 
                                   value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                            <button type="submit">🔍</button>
                        </form>
                    </div>

                    <!-- Admin Access Button - Always Visible -->
                    <div class="admin-access">
                        <?php if (is_logged_in() && is_admin()): ?>
                            <a href="<?php echo $base_path; ?>admin/dashboard.php" class="btn-admin-dashboard">
                                📊 Admin Panel
                            </a>
                        <?php else: ?>
                            <a href="<?php echo $base_path; ?>admin/login.php" class="btn-admin-login">
                                🔐 Admin
                            </a>
                        <?php endif; ?>
                    </div>

                    <?php if (is_logged_in()): ?>
                        <?php if (!is_admin()): ?>
                            <div class="cart-icon">
                                <a href="<?php echo $base_path; ?>user/cart.php" class="cart-link">
                                    🛒 Cart
                                    <?php if ($cart_count > 0): ?>
                                        <span class="cart-count"><?php echo $cart_count; ?></span>
                                    <?php endif; ?>
                                </a>
                            </div>
                        <?php endif; ?>

                        <div class="user-menu">
                            <a href="#" class="user-dropdown">
                                👤 <?php echo htmlspecialchars($_SESSION['user_name']); ?> ▼
                            </a>
                            <ul class="user-dropdown-menu">
                                <?php if (is_admin()): ?>
                                    <li><a href="<?php echo $base_path; ?>admin/dashboard.php">📊 Admin Dashboard</a></li>
                                <?php else: ?>
                                    <li><a href="<?php echo $base_path; ?>user/dashboard.php">👤 My Account</a></li>
                                    <li><a href="<?php echo $base_path; ?>user/cart.php">🛒 My Cart</a></li>
                                    <li><a href="<?php echo $base_path; ?>user/orders.php">📦 My Orders</a></li>
                                <?php endif; ?>
                                <li><a href="<?php echo $base_path; ?>auth/logout.php">🚪 Logout</a></li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <div class="auth-buttons">
                            <a href="<?php echo $base_path; ?>auth/login.php" class="btn btn-outline">Login</a>
                            <a href="<?php echo $base_path; ?>auth/register.php" class="btn btn-primary">Register</a>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="hamburger">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>
        </nav>
    </header>

    <main class="main-content">
        <?php display_message(); ?>
