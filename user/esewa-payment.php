<?php
$page_title = "eSewa Payment";
require_once '../includes/header.php';
require_once '../includes/auth.php';
require_user();

if (!isset($_SESSION['order_id']) || !isset($_SESSION['total_price'])) {
    header('Location: cart.php');
    exit();
}

$order_id = $_SESSION['order_id'];
$total_price = $_SESSION['total_price'];

// eSewa configuration (Sandbox)
$merchant_code = "EPAYTEST";
$success_url = "http://localhost/booknest/user/esewa-success.php";
$failure_url = "http://localhost/booknest/user/esewa-failure.php";

// Generate transaction ID
$transaction_id = "BOOK" . time() . $order_id;
?>

<div class="container">
    <div class="payment-page">
        <div class="page-header">
            <h1>eSewa Payment</h1>
            <p>Complete your payment securely through eSewa</p>
        </div>

        <div class="payment-container">
            <div class="payment-info">
                <div class="order-details">
                    <h3>Order Details</h3>
                    <div class="detail-row">
                        <span>Order ID:</span>
                        <span>#<?php echo str_pad($order_id, 6, '0', STR_PAD_LEFT); ?></span>
                    </div>
                    <div class="detail-row">
                        <span>Transaction ID:</span>
                        <span><?php echo $transaction_id; ?></span>
                    </div>
                    <div class="detail-row">
                        <span>Amount:</span>
                        <span class="amount">₹<?php echo number_format($total_price, 2); ?></span>
                    </div>
                    <div class="detail-row">
                        <span>Payment Method:</span>
                        <span>eSewa</span>
                    </div>
                </div>

                <div class="payment-steps">
                    <h3>How to Pay</h3>
                    <ol>
                        <li>Click on "Proceed to eSewa" button below</li>
                        <li>You will be redirected to eSewa payment gateway</li>
                        <li>Login with your eSewa account</li>
                        <li>Confirm the payment details</li>
                        <li>Complete the payment</li>
                        <li>You will be redirected back to our site</li>
                    </ol>
                </div>

                <div class="test-credentials">
                    <h3>Test Credentials (Sandbox)</h3>
                    <p><strong>eSewa ID:</strong> 9806800000</p>
                    <p><strong>Password:</strong> Nepal@123</p>
                    <p><strong>MPIN:</strong> 1234</p>
                    <small class="note">Use these credentials for testing purposes only</small>
                </div>
            </div>

            <div class="payment-form">
                <h3>Ready to Pay?</h3>
                <p class="payment-amount">Total Amount: <strong>₹<?php echo number_format($total_price, 2); ?></strong></p>
                
                <form action="https://uat.esewa.com.np/epay/main" method="POST" class="esewa-form">
                    <input type="hidden" name="amt" value="<?php echo $total_price; ?>">
                    <input type="hidden" name="psc" value="0">
                    <input type="hidden" name="pdc" value="0">
                    <input type="hidden" name="txAmt" value="0">
                    <input type="hidden" name="tAmt" value="<?php echo $total_price; ?>">
                    <input type="hidden" name="pid" value="<?php echo $transaction_id; ?>">
                    <input type="hidden" name="scd" value="<?php echo $merchant_code; ?>">
                    <input type="hidden" name="su" value="<?php echo $success_url; ?>">
                    <input type="hidden" name="fu" value="<?php echo $failure_url; ?>">
                    
                    <button type="submit" class="btn btn-primary btn-large">
                        <span class="esewa-icon">💳</span>
                        Proceed to eSewa
                    </button>
                </form>

                <div class="payment-actions">
                    <a href="checkout.php" class="btn btn-outline">← Back to Checkout</a>
                    <a href="cancel-payment.php" class="btn btn-danger">Cancel Payment</a>
                </div>
            </div>
        </div>

        <div class="security-note">
            <div class="security-icon">🔒</div>
            <div>
                <h4>Secure Payment</h4>
                <p>Your payment information is secure and encrypted. eSewa uses industry-standard security measures to protect your transactions.</p>
            </div>
        </div>
    </div>
</div>

<style>
.payment-page {
    max-width: 1000px;
    margin: 0 auto;
    padding: 20px 0;
}

.payment-container {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 30px;
    margin-top: 30px;
}

.payment-info {
    background: #f8f9fa;
    padding: 25px;
    border-radius: 10px;
}

.order-details, .payment-steps, .test-credentials {
    margin-bottom: 25px;
}

.order-details h3, .payment-steps h3, .test-credentials h3 {
    margin-bottom: 15px;
    color: #333;
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
    font-size: 1.2em;
    font-weight: bold;
    color: #28a745;
}

.payment-steps ol {
    padding-left: 20px;
}

.payment-steps li {
    margin-bottom: 8px;
}

.test-credentials {
    background: #fff3cd;
    padding: 15px;
    border-radius: 5px;
    border-left: 4px solid #ffc107;
}

.test-credentials p {
    margin: 5px 0;
}

.note {
    color: #856404;
    font-style: italic;
}

.payment-form {
    text-align: center;
}

.payment-amount {
    font-size: 1.5em;
    margin-bottom: 25px;
}

.esewa-form {
    margin-bottom: 25px;
}

.btn-large {
    font-size: 1.1em;
    padding: 15px 30px;
    display: inline-flex;
    align-items: center;
    gap: 10px;
}

.esewa-icon {
    font-size: 1.2em;
}

.payment-actions {
    display: flex;
    gap: 10px;
    justify-content: center;
}

.security-note {
    display: flex;
    align-items: center;
    gap: 15px;
    background: #d1ecf1;
    padding: 20px;
    border-radius: 10px;
    margin-top: 30px;
}

.security-icon {
    font-size: 2em;
}

@media (max-width: 768px) {
    .payment-container {
        grid-template-columns: 1fr;
    }
    
    .payment-actions {
        flex-direction: column;
    }
}
</style>

<?php require_once '../includes/footer.php'; ?>
