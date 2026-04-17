<?php
$page_title = "My Orders";
require_once '../includes/header.php';
require_once '../includes/auth.php';
require_user();

$user_id = $_SESSION['user_id'];

// Get all orders with pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 10;
$offset = ($page - 1) * $per_page;

$count_query = "SELECT COUNT(*) as total FROM orders WHERE user_id = $user_id";
$count_result = $conn->query($count_query);
$total_orders = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_orders / $per_page);

$orders_query = "SELECT o.*, COUNT(oi.id) as item_count FROM orders o 
                 LEFT JOIN order_items oi ON o.id = oi.order_id 
                 WHERE o.user_id = $user_id 
                 GROUP BY o.id 
                 ORDER BY o.created_at DESC 
                 LIMIT $per_page OFFSET $offset";
$orders_result = $conn->query($orders_query);
?>

<div class="container">
    <div class="orders-page">
        <div class="page-header">
            <h1>My Orders</h1>
            <p>Track and manage your orders</p>
        </div>

        <?php if ($orders_result && $orders_result->num_rows > 0): ?>
            <div class="orders-list">
                <?php while ($order = $orders_result->fetch_assoc()): ?>
                    <div class="order-card">
                        <div class="order-header">
                            <div class="order-info">
                                <strong>Order #<?php echo str_pad($order['id'], 6, '0', STR_PAD_LEFT); ?></strong>
                                <span class="order-date"><?php echo date('M d, Y h:i A', strtotime($order['created_at'])); ?></span>
                            </div>
                            <span class="order-status status-<?php echo $order['status']; ?>">
                                <?php echo ucfirst($order['status']); ?>
                            </span>
                        </div>
                        
                        <div class="order-details">
                            <div class="order-summary">
                                <div class="summary-item">
                                    <span class="label">Items:</span>
                                    <span class="value"><?php echo $order['item_count']; ?></span>
                                </div>
                                <div class="summary-item">
                                    <span class="label">Total:</span>
                                    <span class="value">₹<?php echo number_format($order['total_price'], 2); ?></span>
                                </div>
                                <div class="summary-item">
                                    <span class="label">Payment:</span>
                                    <span class="value"><?php echo ucfirst(str_replace('_', ' ', $order['payment_method'])); ?></span>
                                </div>
                            </div>
                            
                            <div class="order-shipping">
                                <h4>Shipping Address</h4>
                                <p><?php echo htmlspecialchars($order['name']); ?></p>
                                <p><?php echo htmlspecialchars($order['address']); ?></p>
                                <p>📞 <?php echo htmlspecialchars($order['phone']); ?></p>
                                <p>✉️ <?php echo htmlspecialchars($order['email']); ?></p>
                            </div>
                        </div>
                        
                        <div class="order-actions">
                            <a href="order-details.php?id=<?php echo $order['id']; ?>" class="btn btn-outline">View Details</a>
                            <?php if ($order['status'] == 'pending'): ?>
                                <button class="btn btn-danger" onclick="cancelOrder(<?php echo $order['id']; ?>)">Cancel Order</button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>

            <?php if ($total_pages > 1): ?>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?php echo $page - 1; ?>" class="page-link">« Previous</a>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <?php if ($i == $page): ?>
                            <span class="page-link active"><?php echo $i; ?></span>
                        <?php else: ?>
                            <a href="?page=<?php echo $i; ?>" class="page-link"><?php echo $i; ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>

                    <?php if ($page < $total_pages): ?>
                        <a href="?page=<?php echo $page + 1; ?>" class="page-link">Next »</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

        <?php else: ?>
            <div class="no-orders">
                <div class="no-orders-icon">📦</div>
                <h3>No orders yet</h3>
                <p>You haven't placed any orders. Start shopping to see your orders here!</p>
                <a href="../index.php" class="btn btn-primary">Start Shopping</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
function cancelOrder(orderId) {
    if (confirm('Are you sure you want to cancel this order?')) {
        window.location.href = 'cancel-order.php?id=' + orderId;
    }
}
</script>

<?php require_once '../includes/footer.php'; ?>
