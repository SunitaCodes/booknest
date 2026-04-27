<?php
require_once '../includes/auth.php';
require_admin();
require_once '../includes/header.php';
?>

<div class="admin-nav">
    <div class="admin-nav-container">
        <div class="admin-breadcrumb">
            <a href="dashboard.php" class="breadcrumb-link">Admin Dashboard</a>
            <?php if (isset($page_title) && $page_title !== 'Admin Dashboard'): ?>
                <span class="breadcrumb-separator">›</span>
                <span class="breadcrumb-current"><?php echo htmlspecialchars($page_title); ?></span>
            <?php endif; ?>
        </div>
        
        <div class="admin-menu">
            <ul class="admin-nav-links">
                <li><a href="dashboard.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'active' : ''; ?>">📊 Dashboard</a></li>
                <li><a href="manage_products.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'manage_products.php' ? 'active' : ''; ?>">📚 Products</a></li>
                <li><a href="manage_orders.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'manage_orders.php' ? 'active' : ''; ?>">📦 Orders</a></li>
                <li><a href="manage_users.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'manage_users.php' ? 'active' : ''; ?>">👥 Users</a></li>
                <li><a href="../index.php" target="_blank">👁️ View Site</a></li>
            </ul>
        </div>
        
        <div class="admin-user">
            <span class="admin-welcome">Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
            <a href="../auth/logout.php" class="btn btn-sm btn-danger">Logout</a>
        </div>
    </div>
</div>

<div class="admin-content">
    <?php display_message(); ?>
