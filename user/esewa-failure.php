<?php
$page_title = "Payment Failed";
require_once '../includes/header.php';
require_once '../includes/auth.php';
require_user();

// Get eSewa response parameters
$amt = isset($_GET['amt']) ? $_GET['amt'] : '';
$pid = isset($_GET['pid']) ? $_GET['pid'] : '';

// Clear session variables
unset($_SESSION['order_id']);
unset($_SESSION['total_price']);
?>

<div class="container">
    <div class="failure-page">
        <div class="failure-card">
            <div class="failure-icon">❌</div>
            <h1>Payment Failed</h1>
            <p>We're sorry, but your payment could not be processed. Please try again or choose a different payment method.</p>
            
            <?php if (!empty($pid)): ?>
            <div class="payment-info">
                <h3>Payment Information</h3>
                <div class="detail-row">
                    <span>Transaction ID:</span>
                    <span><?php echo htmlspecialchars($pid); ?></span>
                </div>
                <?php if (!empty($amt)): ?>
                <div class="detail-row">
                    <span>Amount:</span>
                    <span>₹<?php echo number_format($amt, 2); ?></span>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <div class="troubleshooting">
                <h3>What might have gone wrong?</h3>
                <ul>
                    <li>Insufficient balance in your eSewa account</li>
                    <li>Incorrect eSewa credentials</li>
                    <li>Network connectivity issues</li>
                    <li>Payment gateway temporarily unavailable</li>
                </ul>
            </div>

            <div class="next-steps">
                <h3>What you can do now?</h3>
                <ul>
                    <li>Check your eSewa account balance</li>
                    <li>Verify your eSewa credentials</li>
                    <li>Try the payment again</li>
                    <li>Choose Cash on Delivery instead</li>
                    <li>Contact our support if the problem persists</li>
                </ul>
            </div>

            <div class="failure-actions">
                <a href="checkout.php" class="btn btn-primary">Try Again</a>
                <a href="cart.php" class="btn btn-outline">View Cart</a>
                <a href="../contact.php" class="btn btn-secondary">Contact Support</a>
            </div>
        </div>
    </div>
</div>

<style>
.failure-page {
    max-width: 600px;
    margin: 0 auto;
    padding: 40px 0;
}

.failure-card {
    background: white;
    border-radius: 15px;
    padding: 40px;
    text-align: center;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
}

.failure-icon {
    font-size: 4em;
    margin-bottom: 20px;
}

.failure-card h1 {
    color: #dc3545;
    margin-bottom: 15px;
}

.failure-card p {
    color: #666;
    margin-bottom: 30px;
    font-size: 1.1em;
}

.payment-info, .troubleshooting, .next-steps {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 10px;
    margin-bottom: 20px;
    text-align: left;
}

.payment-info h3, .troubleshooting h3, .next-steps h3 {
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

.troubleshooting ul, .next-steps ul {
    padding-left: 20px;
}

.troubleshooting li, .next-steps li {
    margin-bottom: 8px;
    color: #666;
}

.failure-actions {
    display: flex;
    gap: 15px;
    justify-content: center;
    flex-wrap: wrap;
}

@media (max-width: 768px) {
    .failure-actions {
        flex-direction: column;
    }
    
    .failure-card {
        padding: 20px;
    }
}
</style>

<?php require_once '../includes/footer.php'; ?>
