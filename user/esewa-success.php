<?php
$page_title = "Payment Successful";
require_once '../includes/header.php';
require_once '../includes/auth.php';
require_user();

// Get eSewa response parameters
$amt = isset($_GET['amt']) ? $_GET['amt'] : '';
$pid = isset($_GET['pid']) ? $_GET['pid'] : '';
$refId = isset($_GET['refId']) ? $_GET['refId'] : '';

if (empty($amt) || empty($pid) || empty($refId)) {
    $_SESSION['message'] = "Invalid payment response";
    $_SESSION['message_type'] = "error";
    header('Location: cart.php');
    exit();
}

// Extract order ID from transaction ID
$order_id = 0;
if (preg_match('/BOOK(\d+)(\d+)$/', $pid, $matches)) {
    $order_id = (int)$matches[2];
}

if ($order_id == 0) {
    $_SESSION['message'] = "Invalid order information";
    $_SESSION['message_type'] = "error";
    header('Location: cart.php');
    exit();
}

// Verify order exists and belongs to user
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

// Update order status to processing (payment received)
$update_query = "UPDATE orders SET status = 'processing' WHERE id = $order_id";
$conn->query($update_query);

// Clear session variables
unset($_SESSION['order_id']);
unset($_SESSION['total_price']);

$_SESSION['message'] = "Payment successful! Your order is being processed.";
$_SESSION['message_type'] = "success";
?>

<div class="container">
    <div class="success-page">
        <div class="success-card">
            <div class="success-icon">✅</div>
            <h1>Payment Successful!</h1>
            <p>Thank you for your payment. Your order has been confirmed and is being processed.</p>
            
            <div class="order-summary">
                <h3>Order Details</h3>
                <div class="detail-row">
                    <span>Order ID:</span>
                    <span>#<?php echo str_pad($order_id, 6, '0', STR_PAD_LEFT); ?></span>
                </div>
                <div class="detail-row">
                    <span>Transaction ID:</span>
                    <span><?php echo htmlspecialchars($pid); ?></span>
                </div>
                <div class="detail-row">
                    <span>eSewa Reference:</span>
                    <span><?php echo htmlspecialchars($refId); ?></span>
                </div>
                <div class="detail-row">
                    <span>Amount Paid:</span>
                    <span class="amount">₹<?php echo number_format($amt, 2); ?></span>
                </div>
                <div class="detail-row">
                    <span>Payment Method:</span>
                    <span>eSewa</span>
                </div>
                <div class="detail-row">
                    <span>Order Status:</span>
                    <span class="status-processing">Processing</span>
                </div>
            </div>

            <div class="next-steps">
                <h3>What's Next?</h3>
                <ul>
                    <li>You will receive an order confirmation email shortly</li>
                    <li>Your order will be shipped within 1-2 business days</li>
                    <li>You can track your order status from your account</li>
                    <li>Estimated delivery: 3-5 business days</li>
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
    max-width: 600px;
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

.order-summary {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 10px;
    margin-bottom: 30px;
    text-align: left;
}

.order-summary h3 {
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

.status-processing {
    color: #007bff;
    font-weight: bold;
}

.next-steps {
    text-align: left;
    margin-bottom: 30px;
}

.next-steps h3 {
    text-align: center;
    margin-bottom: 15px;
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
}

@media (max-width: 768px) {
    .success-actions {
        flex-direction: column;
    }
    
    .success-card {
        padding: 20px;
    }
}
</style>

<?php require_once '../includes/footer.php'; ?>
