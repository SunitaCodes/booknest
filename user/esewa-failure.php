<?php
$page_title = "Payment Failed";
require_once '../includes/auth.php';
require_login();
require_user();

$transaction_id = '';
$total_amount = 0.0;
$status = 'FAILED';

// Prefer v2 payload if present.
$encoded_data = $_GET['data'] ?? $_POST['data'] ?? '';
$encoded_data = str_replace(' ', '+', $encoded_data);
$decoded_json = $encoded_data ? base64_decode($encoded_data, true) : false;
$response = $decoded_json ? json_decode($decoded_json, true) : null;

if (is_array($response)) {
    $transaction_id = $response['transaction_uuid'] ?? '';
    $total_amount = isset($response['total_amount']) ? (float)$response['total_amount'] : 0.0;
    $status = $response['status'] ?? 'FAILED';
} else {
    // Legacy v1 query params.
    $transaction_id = $_GET['pid'] ?? '';
    $total_amount = isset($_GET['amt']) ? (float)$_GET['amt'] : 0.0;
}

unset($_SESSION['order_id']);
unset($_SESSION['total_price']);

require_once '../includes/header.php';
?>

<div class="container">
    <div class="failure-page">
        <div class="failure-card">
            <div class="failure-icon">X</div>
            <h1>Payment Failed</h1>
            <p>We could not complete your eSewa payment. Please check details and try again.</p>
            
            <?php if (!empty($transaction_id) || $total_amount > 0): ?>
            <div class="payment-info">
                <h3>Payment Information</h3>
                <?php if (!empty($transaction_id)): ?>
                <div class="detail-row">
                    <span>Transaction ID:</span>
                    <span><?php echo htmlspecialchars($transaction_id); ?></span>
                </div>
                <?php endif; ?>
                <?php if ($total_amount > 0): ?>
                <div class="detail-row">
                    <span>Amount:</span>
                    <span>Rs <?php echo number_format($total_amount, 2); ?></span>
                </div>
                <?php endif; ?>
                <div class="detail-row">
                    <span>Status:</span>
                    <span><?php echo htmlspecialchars($status); ?></span>
                </div>
            </div>
            <?php endif; ?>

            <div class="troubleshooting">
                <h3>Common Causes</h3>
                <ul>
                    <li>Insufficient eSewa balance.</li>
                    <li>Invalid credentials/MPIN.</li>
                    <li>Captcha timeout or gateway interruption.</li>
                    <li>Sandbox service instability.</li>
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
.failure-page { max-width: 620px; margin: 0 auto; padding: 40px 0; }
.failure-card { background: white; border-radius: 15px; padding: 40px; text-align: center; box-shadow: 0 5px 20px rgba(0,0,0,0.1); }
.failure-icon { font-size: 2.2rem; font-weight: 700; color: #dc3545; margin-bottom: 20px; }
.failure-card h1 { color: #dc3545; margin-bottom: 15px; }
.failure-card p { color: #666; margin-bottom: 30px; font-size: 1.05rem; }
.payment-info, .troubleshooting { background: #f8f9fa; padding: 20px; border-radius: 10px; margin-bottom: 20px; text-align: left; }
.payment-info h3, .troubleshooting h3 { margin-bottom: 15px; text-align: center; }
.detail-row { display: flex; justify-content: space-between; gap: 16px; padding: 8px 0; border-bottom: 1px solid #dee2e6; }
.detail-row:last-child { border-bottom: none; }
.troubleshooting ul { padding-left: 20px; }
.troubleshooting li { margin-bottom: 8px; color: #666; }
.failure-actions { display: flex; gap: 15px; justify-content: center; flex-wrap: wrap; }
@media (max-width: 768px) { .failure-actions { flex-direction: column; } .failure-card { padding: 20px; } }
</style>

<?php require_once '../includes/footer.php'; ?>
