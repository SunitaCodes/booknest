<?php
$page_title = "Order Details";
require_once '../includes/header.php';
require_once '../includes/auth.php';
require_user();

$user_id = $_SESSION['user_id'];
$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$order_id) {
    $_SESSION['message'] = "Invalid order ID.";
    $_SESSION['message_type'] = "error";
    header('Location: orders.php');
    exit();
}

// Get order details
$order_query = "SELECT o.* FROM orders o WHERE o.id = $order_id AND o.user_id = $user_id";
$order_result = $conn->query($order_query);

if (!$order_result || $order_result->num_rows === 0) {
    $_SESSION['message'] = "Order not found.";
    $_SESSION['message_type'] = "error";
    header('Location: orders.php');
    exit();
}

$order = $order_result->fetch_assoc();

// Get order items
$items_query = "SELECT oi.*, p.name, p.image FROM order_items oi 
               JOIN products p ON oi.product_id = p.id 
               WHERE oi.order_id = $order_id";
$items_result = $conn->query($items_query);
?>

<div class="container">
    <div class="order-details-page">
        <div class="page-header">
            <div class="header-left">
                <h1>Order Details</h1>
                <p>View your complete order information</p>
            </div>
            <div class="header-right">
                <a href="orders.php" class="btn btn-outline">← Back to Orders</a>
            </div>
        </div>

        <div class="order-content">
            <!-- Order Information Card -->
            <div class="card">
                <div class="card-header">
                    <h3>Order Information</h3>
                    <span class="order-status status-<?php echo $order['status']; ?>">
                        <?php echo ucfirst($order['status']); ?>
                    </span>
                </div>
                <div class="card-body">
                    <div class="order-info-grid">
                        <div class="info-item">
                            <label>Order ID:</label>
                            <span>#<?php echo str_pad($order['id'], 6, '0', STR_PAD_LEFT); ?></span>
                        </div>
                        <div class="info-item">
                            <label>Order Date:</label>
                            <span><?php echo date('M d, Y h:i A', strtotime($order['created_at'])); ?></span>
                        </div>
                        <div class="info-item">
                            <label>Payment Method:</label>
                            <span><?php echo ucfirst(str_replace('_', ' ', $order['payment_method'])); ?></span>
                        </div>
                        <div class="info-item">
                            <label>Total Amount:</label>
                            <span class="total-amount">₹<?php echo number_format($order['total_price'], 2); ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Customer Information Card -->
            <div class="card">
                <div class="card-header">
                    <h3>Customer Information</h3>
                </div>
                <div class="card-body">
                    <div class="customer-info">
                        <div class="info-item">
                            <label>Name:</label>
                            <span><?php echo htmlspecialchars($order['name']); ?></span>
                        </div>
                        <div class="info-item">
                            <label>Email:</label>
                            <span><?php echo htmlspecialchars($order['email']); ?></span>
                        </div>
                        <div class="info-item">
                            <label>Phone:</label>
                            <span><?php echo htmlspecialchars($order['phone']); ?></span>
                        </div>
                        <div class="info-item">
                            <label>Delivery Address:</label>
                            <span><?php echo htmlspecialchars($order['address']); ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Items Card -->
            <div class="card">
                <div class="card-header">
                    <h3>Order Items (<?php echo $items_result->num_rows; ?> items)</h3>
                </div>
                <div class="card-body">
                    <?php if ($items_result && $items_result->num_rows > 0): ?>
                        <div class="order-items">
                            <?php while ($item = $items_result->fetch_assoc()): ?>
                                <div class="order-item">
                                    <div class="item-image">
                                        <img src="../assets/images/<?php echo htmlspecialchars($item['image']); ?>" 
                                             alt="<?php echo htmlspecialchars($item['name']); ?>"
                                             onerror="this.src='../assets/images/default-book.jpg'">
                                    </div>
                                    <div class="item-details">
                                        <h4><?php echo htmlspecialchars($item['name']); ?></h4>
                                        <p class="item-price">₹<?php echo number_format($item['price'], 2); ?> each</p>
                                    </div>
                                    <div class="item-quantity">
                                        <span>Qty: <?php echo $item['quantity']; ?></span>
                                    </div>
                                    <div class="item-total">
                                        <span>₹<?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                        
                        <div class="order-summary">
                            <div class="summary-row">
                                <span>Subtotal:</span>
                                <span>₹<?php echo number_format($order['total_price'], 2); ?></span>
                            </div>
                            <div class="summary-row">
                                <span>Delivery Fee:</span>
                                <span>Free</span>
                            </div>
                            <div class="summary-row total">
                                <span>Total:</span>
                                <span>₹<?php echo number_format($order['total_price'], 2); ?></span>
                            </div>
                        </div>
                    <?php else: ?>
                        <p class="no-items">No items found in this order.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="order-actions">
                <?php if ($order['status'] === 'pending'): ?>
                    <button class="btn btn-danger" onclick="cancelOrder(<?php echo $order['id']; ?>)">
                        Cancel Order
                    </button>
                <?php endif; ?>
                <a href="orders.php" class="btn btn-secondary">Back to Orders</a>
                <a href="../index.php" class="btn btn-primary">Continue Shopping</a>
            </div>
        </div>
    </div>
</div>

<style>
.order-details-page {
    max-width: 1000px;
    margin: 0 auto;
    padding: 20px;
}

.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 1px solid #eee;
}

.header-left h1 {
    margin: 0 0 5px 0;
    color: #333;
}

.header-left p {
    margin: 0;
    color: #666;
}

.card {
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    margin-bottom: 20px;
    overflow: hidden;
}

.card-header {
    background: #f8f9fa;
    padding: 20px;
    border-bottom: 1px solid #eee;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.card-header h3 {
    margin: 0;
    color: #333;
}

.card-body {
    padding: 20px;
}

.order-info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
}

.info-item {
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.info-item label {
    font-weight: 600;
    color: #666;
    font-size: 0.9em;
}

.info-item span {
    color: #333;
    font-weight: 500;
}

.total-amount {
    color: #28a745;
    font-size: 1.1em;
    font-weight: 700;
}

.customer-info {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
}

.order-items {
    margin-bottom: 20px;
}

.order-item {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 15px;
    border: 1px solid #eee;
    border-radius: 8px;
    margin-bottom: 10px;
}

.item-image img {
    width: 60px;
    height: 80px;
    object-fit: cover;
    border-radius: 5px;
}

.item-details {
    flex: 1;
}

.item-details h4 {
    margin: 0 0 5px 0;
    color: #333;
}

.item-price {
    margin: 0;
    color: #666;
    font-size: 0.9em;
}

.item-quantity {
    color: #666;
    font-weight: 500;
}

.item-total {
    font-weight: 600;
    color: #333;
}

.order-summary {
    border-top: 2px solid #eee;
    padding-top: 20px;
}

.summary-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 10px;
}

.summary-row.total {
    font-weight: 700;
    font-size: 1.1em;
    color: #333;
    border-top: 1px solid #eee;
    padding-top: 10px;
}

.order-actions {
    display: flex;
    gap: 15px;
    justify-content: flex-end;
    margin-top: 30px;
}

.no-items {
    text-align: center;
    color: #666;
    padding: 40px;
}

@media (max-width: 768px) {
    .page-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 15px;
    }
    
    .order-info-grid,
    .customer-info {
        grid-template-columns: 1fr;
    }
    
    .order-item {
        flex-direction: column;
        text-align: center;
    }
    
    .order-actions {
        flex-direction: column;
    }
}
</style>

<script>
function cancelOrder(orderId) {
    if (confirm('Are you sure you want to cancel this order? This action cannot be undone.')) {
        // Create form to submit cancellation
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'cancel-order.php';
        form.innerHTML = '<input type="hidden" name="order_id" value="' + orderId + '">';
        document.body.appendChild(form);
        form.submit();
    }
}
</script>

<?php require_once '../includes/footer.php'; ?>
