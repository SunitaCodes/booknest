<?php
$page_title = "eSewa Payment";
require_once '../includes/auth.php';
require_login();
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
$secret       = "8gBm/:&EnhH.1/q";
// Generate transaction ID
$transaction_id = "BOOK" . time() . $order_id;

$message   = "total_amount=$total_price,transaction_uuid=$transaction_id,product_code=$merchant_code";
$signature = base64_encode(hash_hmac('sha256', $message, $secret, true));

require_once '../includes/header.php';
?>

<div class="container">
    <div class="esewa-page">
        <div class="page-header">
            <h1>eSewa Payment</h1>
            <p>Review your details and complete payment in one secure step.</p>
        </div>

        <div class="esewa-layout">
            <div class="esewa-left">
                <section class="esewa-card order-details">
                    <h3>Order Details</h3>
                    <div class="detail-row">
                        <span>Order ID</span>
                        <span>#<?php echo str_pad($order_id, 6, '0', STR_PAD_LEFT); ?></span>
                    </div>
                    <div class="detail-row">
                        <span>Transaction ID</span>
                        <span><?php echo $transaction_id; ?></span>
                    </div>
                    <div class="detail-row">
                        <span>Amount</span>
                        <span class="amount">Rs <?php echo number_format($total_price, 2); ?></span>
                    </div>
                    <div class="detail-row">
                        <span>Payment Method</span>
                        <span>eSewa</span>
                    </div>
                </section>

                <section class="esewa-card payment-steps">
                    <h3>How to Pay</h3>
                    <ol>
                        <li>Click "Proceed to eSewa".</li>
                        <li>You will be redirected to the eSewa gateway.</li>
                        <li>Login with your eSewa account.</li>
                        <li>Confirm payment details and complete payment.</li>
                        <li>You will return here after payment status is received.</li>
                    </ol>
                </section>

                <section class="esewa-card test-credentials">
                    <h3>Sandbox Credentials</h3>
                    <div class="credential-row"><span>eSewa IDs</span><strong>9806800001 / 9806800002 / 9806800003 / 9806800004 / 9806800005</strong></div>
                    <div class="credential-row"><span>Password</span><strong>Nepal@123</strong></div>
                    <div class="credential-row"><span>OTP</span><strong>123456</strong></div>
                    <div class="credential-row"><span>MPIN</span><strong>1122</strong></div>
                    <small class="note">Use only for test payments in sandbox.</small>
                </section>
            </div>

            <aside class="esewa-card esewa-right">
                <h3>Ready to Pay?</h3>
                <p class="payment-amount">Total Amount</p>
                <p class="payment-amount-value">Rs <?php echo number_format($total_price, 2); ?></p>
                <p class="gateway-badge">UAT Sandbox Gateway</p>

                <form action="https://rc-epay.esewa.com.np/api/epay/main/v2/form" method="POST" class="esewa-form">
                    <input type="hidden" name="amount" value="<?php echo $total_price; ?>">
                    <input type="hidden" name="product_service_charge" value="0">
                    <input type="hidden" name="product_delivery_charge" value="0">
                    <input type="hidden" name="tax_amount" value="0">
                    <input type="hidden" name="total_amount" value="<?php echo $total_price; ?>">
                    <input type="hidden" name="transaction_uuid" value="<?php echo $transaction_id; ?>">
                    <input type="hidden" name="product_code" value="<?php echo $merchant_code; ?>">
                    <input type="hidden" name="success_url" value="<?php echo $success_url; ?>">
                    <input type="hidden" name="failure_url" value="<?php echo $failure_url; ?>">
                    <input type="hidden" name="signed_field_names" value="total_amount,transaction_uuid,product_code">
                    <input type="hidden" name="signature" value="<?php echo $signature; ?>">

                    <button type="submit" class="btn btn-primary btn-large esewa-submit">
                        <span class="esewa-icon">Pay</span>
                        Proceed to eSewa
                    </button>
                </form>

                <div class="payment-actions">
                    <a href="checkout.php" class="btn btn-outline">Back to Checkout</a>
                    <a href="cancel-payment.php" class="btn btn-danger">Cancel Payment</a>
                </div>
            </aside>
        </div>

        <div class="security-note">
            <div class="security-icon">🔒</div>
            <div>
                <h4>Secure Payment</h4>
                <p>Payment is handled by eSewa over encrypted channels. This site does not store your eSewa credentials.</p>
            </div>
        </section>
    </div>
</div>

<style>
.esewa-page {
    max-width: 1100px;
    margin: 0 auto;
    padding: 24px 0 8px;
}

.esewa-layout {
    display: grid;
    grid-template-columns: minmax(0, 1.35fr) minmax(280px, 0.85fr);
    gap: 20px;
    margin-top: 22px;
    align-items: start;
}

.esewa-left {
    display: grid;
    gap: 16px;
}

.esewa-card {
    background: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 14px;
    padding: 18px;
    box-shadow: 0 10px 28px rgba(15, 23, 42, 0.05);
}

.order-details h3,
.payment-steps h3,
.test-credentials h3 {
    margin-bottom: 12px;
    color: #111827;
}

.detail-row {
    display: flex;
    justify-content: space-between;
    gap: 12px;
    padding: 10px 0;
    border-bottom: 1px solid #eceff3;
}

.detail-row span:first-child {
    color: #6b7280;
}

.detail-row:last-child {
    border-bottom: none;
}

.amount {
    font-size: 1.14rem;
    font-weight: 700;
    color: #16a34a;
}

.payment-steps ol {
    padding-left: 18px;
    margin: 0;
    color: #374151;
}

.payment-steps li {
    margin-bottom: 7px;
}

.test-credentials {
    background: linear-gradient(180deg, #fef9c3 0%, #fff7d6 100%);
    border-color: #f4d03f;
}

.credential-row {
    display: flex;
    justify-content: space-between;
    gap: 10px;
    padding: 7px 0;
    border-bottom: 1px dashed rgba(146, 113, 20, 0.35);
}

.credential-row:last-of-type {
    border-bottom: none;
}

.credential-row span {
    color: #7c5b1f;
}

.note {
    display: block;
    margin-top: 8px;
    color: #8a6d1f;
    font-style: italic;
}

.esewa-right {
    text-align: center;
    position: sticky;
    top: 92px;
}

.payment-amount {
    margin: 2px 0 6px;
    color: #6b7280;
}

.payment-amount-value {
    margin: 0 0 10px;
    font-size: 2rem;
    font-weight: 700;
    color: #111827;
}

.gateway-badge {
    display: inline-block;
    margin: 0 0 14px;
    background: #dcfce7;
    color: #166534;
    padding: 6px 10px;
    border-radius: 999px;
    font-size: 0.82rem;
    font-weight: 600;
}

.esewa-form {
    margin-bottom: 16px;
}

.esewa-submit {
    width: 100%;
    justify-content: center;
}

.payment-actions {
    display: grid;
    gap: 10px;
}

.security-note {
    display: flex;
    align-items: flex-start;
    gap: 15px;
    margin-top: 18px;
}

.security-icon {
    background: #e6f4ff;
    color: #1d4ed8;
    border-radius: 999px;
    padding: 6px 10px;
    font-size: 0.8rem;
    font-weight: 700;
    line-height: 1;
    margin-top: 2px;
}

.security-note h4 {
    margin-bottom: 4px;
}

.security-note p {
    margin: 0;
    color: #4b5563;
}

@media (max-width: 768px) {
    .esewa-layout {
        grid-template-columns: 1fr;
    }

    .esewa-right {
        position: static;
    }

    .payment-actions {
        grid-template-columns: 1fr;
    }
}
</style>

<?php require_once '../includes/footer.php'; ?>
