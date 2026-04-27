<?php
$page_title = "Payment Successful";
require_once '../includes/auth.php';
require_login();
require_user();

$merchant_code = "EPAYTEST";
$secret = "8gBm/:&EnhH.1/q";

$transaction_uuid = '';
$paid_amount = 0.0;
$reference_id = '';
$response_mode = '';

// V2 response (base64 JSON in data).
$encoded_data = $_GET['data'] ?? $_POST['data'] ?? '';
$encoded_data = str_replace(' ', '+', $encoded_data);
$decoded_json = $encoded_data ? base64_decode($encoded_data, true) : false;
$response = $decoded_json ? json_decode($decoded_json, true) : null;

if (is_array($response) && isset($response['transaction_uuid'])) {
    $response_mode = 'v2';
    $required_fields = ['status', 'transaction_uuid', 'total_amount', 'product_code', 'signed_field_names', 'signature'];
    foreach ($required_fields as $field) {
        if (!isset($response[$field]) || $response[$field] === '') {
            $_SESSION['message'] = "Incomplete payment response.";
            $_SESSION['message_type'] = "error";
            header('Location: cart.php');
            exit();
        }
    }

    // Verify v2 signature.
    $signed_names = array_map('trim', explode(',', $response['signed_field_names']));
    $message_parts = [];
    foreach ($signed_names as $name) {
        if ($name === '' || !array_key_exists($name, $response)) {
            $_SESSION['message'] = "Invalid signed fields in payment response.";
            $_SESSION['message_type'] = "error";
            header('Location: cart.php');
            exit();
        }
        $message_parts[] = $name . '=' . $response[$name];
    }
    $expected_signature = base64_encode(hash_hmac('sha256', implode(',', $message_parts), $secret, true));
    if (!hash_equals($expected_signature, $response['signature'])) {
        $_SESSION['message'] = "Payment verification failed (signature mismatch).";
        $_SESSION['message_type'] = "error";
        header('Location: cart.php');
        exit();
    }

    if ($response['product_code'] !== $merchant_code || $response['status'] !== 'COMPLETE') {
        $_SESSION['message'] = "Payment was not completed.";
        $_SESSION['message_type'] = "error";
        header('Location: esewa-failure.php?data=' . urlencode($encoded_data));
        exit();
    }

    $transaction_uuid = $response['transaction_uuid'];
    $paid_amount = (float)$response['total_amount'];
    $reference_id = $response['transaction_code'] ?? '';
} else {
    // Legacy v1 response.
    $response_mode = 'v1';
    $amt = $_GET['amt'] ?? '';
    $pid = $_GET['pid'] ?? '';
    $refId = $_GET['refId'] ?? '';
    if ($amt === '' || $pid === '' || $refId === '') {
        $_SESSION['message'] = "Invalid payment response.";
        $_SESSION['message_type'] = "error";
        header('Location: cart.php');
        exit();
    }
    $transaction_uuid = $pid;
    $paid_amount = (float)$amt;
    $reference_id = $refId;
}

// Extract order ID from transaction UUID.
$order_id = 0;
if (preg_match('/^BOOK-(\d+)-[A-Za-z0-9-]+$/', $transaction_uuid, $matches)) {
    $order_id = (int)$matches[1];
} elseif (preg_match('/BOOK(\d+)(\d+)$/', $transaction_uuid, $matches)) {
    // Backward compatibility for old BOOK<time><order_id> format.
    $order_id = (int)$matches[2];
}

if ($order_id <= 0) {
    $_SESSION['message'] = "Invalid order information.";
    $_SESSION['message_type'] = "error";
    header('Location: cart.php');
    exit();
}

$user_id = (int)$_SESSION['user_id'];
$order_query = "SELECT * FROM orders WHERE id = $order_id AND user_id = $user_id";
$order_result = $conn->query($order_query);
if (!$order_result || $order_result->num_rows === 0) {
    $_SESSION['message'] = "Order not found.";
    $_SESSION['message_type'] = "error";
    header('Location: cart.php');
    exit();
}

$order = $order_result->fetch_assoc();
$order_amount = (float)$order['total_price'];
if (abs($paid_amount - $order_amount) > 0.01) {
    $_SESSION['message'] = "Payment amount mismatch.";
    $_SESSION['message_type'] = "error";
    header('Location: cart.php');
    exit();
}

$update_query = "UPDATE orders SET status = 'processing' WHERE id = $order_id";
$conn->query($update_query);

unset($_SESSION['order_id']);
unset($_SESSION['total_price']);

$_SESSION['message'] = "Payment successful! Your order is being processed.";
$_SESSION['message_type'] = "success";

require_once '../includes/header.php';
?>

<div class="container">
    <div class="success-page">
        <div class="success-card">
            <div class="success-icon">OK</div>
            <h1>Payment Successful!</h1>
            <p>Your payment has been confirmed and your order is now processing.</p>
            
            <div class="order-summary">
                <h3>Order Details</h3>
                <div class="detail-row">
                    <span>Order ID:</span>
                    <span>#<?php echo str_pad($order_id, 6, '0', STR_PAD_LEFT); ?></span>
                </div>
                <div class="detail-row">
                    <span>Transaction ID:</span>
                    <span><?php echo htmlspecialchars($transaction_uuid); ?></span>
                </div>
                <div class="detail-row">
                    <span>Reference:</span>
                    <span><?php echo htmlspecialchars($reference_id); ?></span>
                </div>
                <div class="detail-row">
                    <span>Amount Paid:</span>
                    <span class="amount">Rs <?php echo number_format($paid_amount, 2); ?></span>
                </div>
                <div class="detail-row">
                    <span>Gateway Mode:</span>
                    <span><?php echo strtoupper($response_mode); ?></span>
                </div>
                <div class="detail-row">
                    <span>Order Status:</span>
                    <span class="status-processing">Processing</span>
                </div>
            </div>

            <div class="success-actions">
                <a href="orders.php" class="btn btn-primary">View My Orders</a>
                <a href="../index.php" class="btn btn-outline">Continue Shopping</a>
            </div>
        </div>
    </div>
</div>

<style>
.success-page { max-width: 620px; margin: 0 auto; padding: 40px 0; }
.success-card { background: white; border-radius: 15px; padding: 40px; text-align: center; box-shadow: 0 5px 20px rgba(0,0,0,0.1); }
.success-icon { font-size: 2.2rem; font-weight: 700; color: #1f9d55; margin-bottom: 20px; }
.success-card h1 { color: #28a745; margin-bottom: 15px; }
.success-card p { color: #666; margin-bottom: 30px; font-size: 1.05rem; }
.order-summary { background: #f8f9fa; padding: 20px; border-radius: 10px; margin-bottom: 30px; text-align: left; }
.order-summary h3 { margin-bottom: 15px; text-align: center; }
.detail-row { display: flex; justify-content: space-between; gap: 16px; padding: 8px 0; border-bottom: 1px solid #dee2e6; }
.detail-row:last-child { border-bottom: none; }
.amount { font-size: 1.1rem; font-weight: 700; color: #28a745; }
.status-processing { color: #007bff; font-weight: 700; }
.success-actions { display: flex; gap: 15px; justify-content: center; }
@media (max-width: 768px) { .success-actions { flex-direction: column; } .success-card { padding: 20px; } }
</style>

<?php require_once '../includes/footer.php'; ?>
