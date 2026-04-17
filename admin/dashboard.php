<?php
$page_title = "Admin Dashboard";
require_once 'includes/admin-header.php';

// Get dashboard statistics
$total_products_query = "SELECT COUNT(*) as count FROM products";
$total_products = $conn->query($total_products_query)->fetch_assoc()['count'];

$total_orders_query = "SELECT COUNT(*) as count FROM orders";
$total_orders = $conn->query($total_orders_query)->fetch_assoc()['count'];

$total_users_query = "SELECT COUNT(*) as count FROM users WHERE role = 'user'";
$total_users = $conn->query($total_users_query)->fetch_assoc()['count'];

$total_revenue_query = "SELECT SUM(total_price) as revenue FROM orders WHERE status != 'cancelled'";
$total_revenue = $conn->query($total_revenue_query)->fetch_assoc()['revenue'];

$pending_orders_query = "SELECT COUNT(*) as count FROM orders WHERE status = 'pending'";
$pending_orders = $conn->query($pending_orders_query)->fetch_assoc()['count'];

$low_stock_query = "SELECT COUNT(*) as count FROM products WHERE stock <= 5";
$low_stock = $conn->query($low_stock_query)->fetch_assoc()['count'];

// Get recent orders
$recent_orders_query = "SELECT o.*, u.full_name FROM orders o 
                        JOIN users u ON o.user_id = u.id 
                        ORDER BY o.created_at DESC LIMIT 5";
$recent_orders = $conn->query($recent_orders_query);

// Get top selling products
$top_products_query = "SELECT p.name, SUM(oi.quantity) as total_sold 
                       FROM order_items oi 
                       JOIN products p ON oi.product_id = p.id 
                       GROUP BY p.id, p.name 
                       ORDER BY total_sold DESC LIMIT 5";
$top_products = $conn->query($top_products_query);
?>

<div class="admin-dashboard">
    <div class="dashboard-header">
        <h1>Admin Dashboard</h1>
        <p>Welcome back, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</p>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">📚</div>
            <div class="stat-info">
                <h3><?php echo $total_products; ?></h3>
                <p>Total Products</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">📦</div>
            <div class="stat-info">
                <h3><?php echo $total_orders; ?></h3>
                <p>Total Orders</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">👥</div>
            <div class="stat-info">
                <h3><?php echo $total_users; ?></h3>
                <p>Total Users</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">💰</div>
            <div class="stat-info">
                <h3>₹<?php echo number_format($total_revenue, 2); ?></h3>
                <p>Total Revenue</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">⏰</div>
            <div class="stat-info">
                <h3><?php echo $pending_orders; ?></h3>
                <p>Pending Orders</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">⚠️</div>
            <div class="stat-info">
                <h3><?php echo $low_stock; ?></h3>
                <p>Low Stock Items</p>
            </div>
        </div>
    </div>

    <div class="dashboard-content">
        <div class="recent-orders">
            <div class="section-header">
                <h3>Recent Orders</h3>
                <a href="manage_orders.php" class="btn btn-sm btn-outline">View All</a>
            </div>
            
            <?php if ($recent_orders && $recent_orders->num_rows > 0): ?>
                <div class="orders-table">
                    <table>
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($order = $recent_orders->fetch_assoc()): ?>
                                <tr>
                                    <td>#<?php echo str_pad($order['id'], 6, '0', STR_PAD_LEFT); ?></td>
                                    <td><?php echo htmlspecialchars($order['full_name']); ?></td>
                                    <td>₹<?php echo number_format($order['total_price'], 2); ?></td>
                                    <td>
                                        <span class="status-badge status-<?php echo $order['status']; ?>">
                                            <?php echo ucfirst($order['status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="no-data">No orders yet</p>
            <?php endif; ?>
        </div>

        <div class="top-products">
            <div class="section-header">
                <h3>Top Selling Products</h3>
                <a href="manage_products.php" class="btn btn-sm btn-outline">Manage Products</a>
            </div>
            
            <?php if ($top_products && $top_products->num_rows > 0): ?>
                <div class="products-list">
                    <?php while ($product = $top_products->fetch_assoc()): ?>
                        <div class="product-item">
                            <div class="product-name"><?php echo htmlspecialchars($product['name']); ?></div>
                            <div class="product-sold"><?php echo $product['total_sold']; ?> sold</div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <p class="no-data">No sales data yet</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="quick-actions">
        <h3>Quick Actions</h3>
        <div class="actions-grid">
            <a href="manage_products.php?action=add" class="action-card">
                <div class="action-icon">➕</div>
                <h4>Add Product</h4>
                <p>Add new books to inventory</p>
            </a>
            
            <a href="manage_orders.php?status=pending" class="action-card">
                <div class="action-icon">📋</div>
                <h4>Process Orders</h4>
                <p>Manage pending orders</p>
            </a>
            
            <a href="manage_products.php?filter=low_stock" class="action-card">
                <div class="action-icon">⚠️</div>
                <h4>Low Stock Alert</h4>
                <p>Restock items running low</p>
            </a>
            
            <a href="manage_users.php" class="action-card">
                <div class="action-icon">👥</div>
                <h4>Manage Users</h4>
                <p>View and manage customers</p>
            </a>
        </div>
    </div>
</div>

<style>
.admin-dashboard {
    max-width: 1400px;
    margin: 0 auto;
    padding: 20px;
}

.dashboard-header {
    margin-bottom: 30px;
}

.dashboard-header h1 {
    color: #333;
    margin-bottom: 5px;
}

.dashboard-header p {
    color: #666;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: white;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    display: flex;
    align-items: center;
    gap: 15px;
}

.stat-icon {
    font-size: 2em;
    width: 50px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f8f9fa;
    border-radius: 10px;
}

.stat-info h3 {
    margin: 0;
    font-size: 1.5em;
    color: #333;
}

.stat-info p {
    margin: 0;
    color: #666;
    font-size: 0.9em;
}

.dashboard-content {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 30px;
    margin-bottom: 30px;
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.section-header h3 {
    margin: 0;
    color: #333;
}

.orders-table {
    background: white;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.orders-table table {
    width: 100%;
    border-collapse: collapse;
}

.orders-table th,
.orders-table td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #dee2e6;
}

.orders-table th {
    background: #f8f9fa;
    font-weight: 600;
}

.status-badge {
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 0.8em;
    font-weight: 600;
}

.status-pending {
    background: #fff3cd;
    color: #856404;
}

.status-processing {
    background: #cce5ff;
    color: #004085;
}

.status-shipped {
    background: #d1ecf1;
    color: #0c5460;
}

.status-delivered {
    background: #d4edda;
    color: #155724;
}

.status-cancelled {
    background: #f8d7da;
    color: #721c24;
}

.top-products {
    background: white;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.products-list {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.product-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px;
    background: #f8f9fa;
    border-radius: 5px;
}

.product-name {
    font-weight: 500;
}

.product-sold {
    color: #666;
    font-size: 0.9em;
}

.quick-actions {
    background: white;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.quick-actions h3 {
    margin: 0 0 20px 0;
    color: #333;
}

.actions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
}

.action-card {
    display: block;
    padding: 20px;
    background: #f8f9fa;
    border-radius: 10px;
    text-align: center;
    text-decoration: none;
    color: inherit;
    transition: all 0.3s ease;
}

.action-card:hover {
    background: #e9ecef;
    transform: translateY(-2px);
}

.action-icon {
    font-size: 2em;
    margin-bottom: 10px;
}

.action-card h4 {
    margin: 0 0 5px 0;
    color: #333;
}

.action-card p {
    margin: 0;
    color: #666;
    font-size: 0.9em;
}

.no-data {
    text-align: center;
    color: #666;
    padding: 20px;
}

@media (max-width: 768px) {
    .dashboard-content {
        grid-template-columns: 1fr;
    }
    
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .actions-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}
</style>

<?php require_once 'includes/admin-footer.php'; ?>
