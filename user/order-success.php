<?php
$page_title = "Order Successful";
require_once '../includes/header.php';
require_once '../includes/auth.php';
require_user();

$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($order_id == 0) {
    header('Location: cart.php');
    exit();
}

// Get order details
$user_id = $_SESSION['user_id'];
$order_query = "SELECT * FROM orders WHERE id = $order_id AND user_id = $user_id";
$order_result = $conn->query($order_query);

if (!$order_result || $order_result->num_rows == 0) {
    $_SESSION['message'] = "Order not found";
    $_SESSION['message_type'] = "error";
    header('Location: cart.php');
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
    <div class="success-page">
        <div class="success-card">
            <div class="success-icon">🎉</div>
            <h1>Order Placed Successfully!</h1>
            <p>Thank you for your order. We've received your request and will process it shortly.</p>
            
            <div class="order-summary">
                <h3>Order Details</h3>
                <div class="detail-row">
                    <span>Order ID:</span>
                    <span>#<?php echo str_pad($order_id, 6, '0', STR_PAD_LEFT); ?></span>
                </div>
                <div class="detail-row">
                    <span>Order Date:</span>
                    <span><?php echo date('M d, Y h:i A', strtotime($order['created_at'])); ?></span>
                </div>
                <div class="detail-row">
                    <span>Total Amount:</span>
                    <span class="amount">₹<?php echo number_format($order['total_price'], 2); ?></span>
                </div>
                <div class="detail-row">
                    <span>Payment Method:</span>
                    <span><?php echo ucfirst(str_replace('_', ' ', $order['payment_method'])); ?></span>
                </div>
                <div class="detail-row">
                    <span>Order Status:</span>
                    <span class="status-<?php echo $order['status']; ?>"><?php echo ucfirst($order['status']); ?></span>
                </div>
            </div>

            <?php if ($items_result && $items_result->num_rows > 0): ?>
            <div class="order-items">
                <h3>Ordered Items</h3>
                <?php while ($item = $items_result->fetch_assoc()): ?>
                <div class="order-item">
                    <img src="../assets/images/<?php echo htmlspecialchars($item['image']); ?>" 
                         alt="<?php echo htmlspecialchars($item['name']); ?>"
                         onerror="this.src='../assets/images/default-book.jpg'">
                    <div class="item-details">
                        <h4><?php echo htmlspecialchars($item['name']); ?></h4>
                        <p>Quantity: <?php echo $item['quantity']; ?></p>
                        <p>Price: ₹<?php echo number_format($item['price'], 2); ?></p>
                    </div>
                    <div class="item-total">
                        ₹<?php echo number_format($item['price'] * $item['quantity'], 2); ?>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
            <?php endif; ?>

            <div class="shipping-info">
                <h3>Shipping Information</h3>
                <p><strong>Name:</strong> <?php echo htmlspecialchars($order['name']); ?></p>
                <p><strong>Address:</strong> <?php echo htmlspecialchars($order['address']); ?></p>
                <p><strong>Phone:</strong> <?php echo htmlspecialchars($order['phone']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($order['email']); ?></p>
            </div>

            <div class="next-steps">
                <h3>What's Next?</h3>
                <ul>
                    <li>You will receive an order confirmation email shortly</li>
                    <li>Your order will be processed within 1-2 business days</li>
                    <li>You can track your order status from your account dashboard</li>
                    <li>Estimated delivery: 3-5 business days after processing</li>
                </ul>
            </div>

            <div class="success-actions">
                <a href="orders.php" class="btn btn-primary">View My Orders</a>
                <a href="../index.php" class="btn btn-outline">Continue Shopping</a>
            </div>
        </div>
    </div>
</div>

<style>
.success-page {
    max-width: 700px;
    margin: 0 auto;
    padding: 40px 0;
}

.success-card {
    background: white;
    border-radius: 15px;
    padding: 40px;
    text-align: center;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
}

.success-icon {
    font-size: 4em;
    margin-bottom: 20px;
}

.success-card h1 {
    color: #28a745;
    margin-bottom: 15px;
}

.success-card p {
    color: #666;
    margin-bottom: 30px;
    font-size: 1.1em;
}

.order-summary, .order-items, .shipping-info, .next-steps {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 10px;
    margin-bottom: 20px;
    text-align: left;
}

.order-summary h3, .order-items h3, .shipping-info h3, .next-steps h3 {
    margin-bottom: 15px;
    text-align: center;
}

.detail-row {
    display: flex;
    justify-content: space-between;
    padding: 8px 0;
    border-bottom: 1px solid #dee2e6;
}

.detail-row:last-child {
    border-bottom: none;
}

.amount {
    font-size: 1.1em;
    font-weight: bold;
    color: #28a745;
}

.status-pending {
    color: #ffc107;
    font-weight: bold;
}

.status-processing {
    color: #007bff;
    font-weight: bold;
}

.order-item {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 10px 0;
    border-bottom: 1px solid #dee2e6;
}

.order-item:last-child {
    border-bottom: none;
}

.order-item img {
    width: 50px;
    height: 70px;
    object-fit: cover;
    border-radius: 5px;
}

.item-details {
    flex: 1;
}

.item-details h4 {
    margin: 0 0 5px 0;
    font-size: 0.9em;
}

.item-details p {
    margin: 2px 0;
    font-size: 0.8em;
    color: #666;
}

.item-total {
    font-weight: bold;
    color: #28a745;
}

.shipping-info p {
    margin: 5px 0;
}

.next-steps ul {
    padding-left: 20px;
}

.next-steps li {
    margin-bottom: 8px;
    color: #666;
}

.success-actions {
    display: flex;
    gap: 15px;
    justify-content: center;
    flex-wrap: wrap;
}

@media (max-width: 768px) {
    .success-actions {
        flex-direction: column;
    }
    
    .success-card {
        padding: 20px;
    }
    
    .order-item {
        flex-direction: column;
        text-align: center;
    }
}
</style>

<?php require_once '../includes/footer.php'; ?>
