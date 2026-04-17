<?php
require_once '../config/db.php';
require_once '../includes/auth.php';
require_admin();

if (!isset($_GET['id'])) {
    exit();
}

$order_id = (int)$_GET['id'];

// Get order details
$order_query = "SELECT o.*, u.full_name, u.email FROM orders o 
                JOIN users u ON o.user_id = u.id 
                WHERE o.id = $order_id";
$order_result = $conn->query($order_query);

if (!$order_result || $order_result->num_rows == 0) {
    echo '<p>Order not found</p>';
    exit();
}

$order = $order_result->fetch_assoc();

// Get order items
$items_query = "SELECT oi.*, p.name, p.image FROM order_items oi 
                JOIN products p ON oi.product_id = p.id 
                WHERE oi.order_id = $order_id";
$items_result = $conn->query($items_query);
?>

<div class="order-detail-section">
    <h4>Order Information</h4>
    <div class="detail-grid">
        <div class="detail-item">
            <div class="detail-label">Order ID</div>
            <div class="detail-value">#<?php echo str_pad($order['id'], 6, '0', STR_PAD_LEFT); ?></div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Order Date</div>
            <div class="detail-value"><?php echo date('M d, Y h:i A', strtotime($order['created_at'])); ?></div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Status</div>
            <div class="detail-value">
                <span class="status-badge status-<?php echo $order['status']; ?>">
                    <?php echo ucfirst($order['status']); ?>
                </span>
            </div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Payment Method</div>
            <div class="detail-value"><?php echo ucfirst(str_replace('_', ' ', $order['payment_method'])); ?></div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Total Amount</div>
            <div class="detail-value"><strong>₹<?php echo number_format($order['total_price'], 2); ?></strong></div>
        </div>
    </div>
</div>

<div class="order-detail-section">
    <h4>Customer Information</h4>
    <div class="detail-grid">
        <div class="detail-item">
            <div class="detail-label">Name</div>
            <div class="detail-value"><?php echo htmlspecialchars($order['name']); ?></div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Email</div>
            <div class="detail-value"><?php echo htmlspecialchars($order['email']); ?></div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Phone</div>
            <div class="detail-value"><?php echo htmlspecialchars($order['phone']); ?></div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Address</div>
            <div class="detail-value"><?php echo htmlspecialchars($order['address']); ?></div>
        </div>
    </div>
</div>

<div class="order-detail-section">
    <h4>Order Items</h4>
    <div class="order-items-list">
        <?php if ($items_result && $items_result->num_rows > 0): ?>
            <?php while ($item = $items_result->fetch_assoc()): ?>
                <div class="order-item-row">
                    <img src="../assets/images/<?php echo htmlspecialchars($item['image']); ?>" 
                         alt="<?php echo htmlspecialchars($item['name']); ?>"
                         class="order-item-image"
                         onerror="this.src='../assets/images/default-book.jpg'">
                    <div class="order-item-details">
                        <div class="order-item-name"><?php echo htmlspecialchars($item['name']); ?></div>
                        <div class="order-item-meta">
                            Quantity: <?php echo $item['quantity']; ?> × 
                            ₹<?php echo number_format($item['price'], 2); ?>
                        </div>
                    </div>
                    <div class="order-item-price">
                        ₹<?php echo number_format($item['price'] * $item['quantity'], 2); ?>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No items found</p>
        <?php endif; ?>
    </div>
</div>

<style>
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
</style>
