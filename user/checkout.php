<?php
$page_title = "Checkout";
require_once '../includes/header.php';
require_once '../includes/auth.php';
require_user();

$user_id = $_SESSION['user_id'];

// Get cart items
$cart_query = "SELECT c.*, p.* FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = $user_id";
$cart_result = $conn->query($cart_query);

if (!$cart_result || $cart_result->num_rows == 0) {
    $_SESSION['message'] = "Your cart is empty!";
    $_SESSION['message_type'] = "error";
    header('Location: cart.php');
    exit();
}

$cart_items = [];
$total_price = 0;

while ($item = $cart_result->fetch_assoc()) {
    // Check stock availability
    if ($item['stock'] < $item['quantity']) {
        $_SESSION['message'] = "Some items in your cart are no longer available in the requested quantity.";
        $_SESSION['message_type'] = "error";
        header('Location: cart.php');
        exit();
    }
    $cart_items[] = $item;
    $total_price += $item['price'] * $item['quantity'];
}

// Get user information
$user_query = "SELECT * FROM users WHERE id = $user_id";
$user_result = $conn->query($user_query);
$user = $user_result->fetch_assoc();

// Handle order placement
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = clean_input($_POST['name']);
    $email = clean_input($_POST['email']);
    $phone = clean_input($_POST['phone']);
    $address = clean_input($_POST['address']);
    $payment_method = clean_input($_POST['payment_method']);
    
    $errors = [];
    
    // Validation
    if (empty($name) || !preg_match("/^[a-zA-Z\s]+$/", $name)) {
        $errors[] = "Name should contain only alphabets";
    }
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Valid email is required";
    }
    
    if (empty($phone) || !preg_match("/^[0-9]{10}$/", $phone)) {
        $errors[] = "Phone number must be exactly 10 digits";
    }
    
    if (empty($address)) {
        $errors[] = "Address is required";
    }
    
    if (!in_array($payment_method, ['cash_on_delivery', 'esewa'])) {
        $errors[] = "Invalid payment method";
    }
    
    if (empty($errors)) {
        // Start transaction
        $conn->begin_transaction();
        
        try {
            // Create order
            $order_query = "INSERT INTO orders (user_id, name, email, phone, address, payment_method, total_price, status) 
                           VALUES ($user_id, '$name', '$email', '$phone', '$address', '$payment_method', $total_price, 'pending')";
            
            if ($conn->query($order_query)) {
                $order_id = $conn->insert_id;
                
                // Add order items
                foreach ($cart_items as $item) {
                    $order_item_query = "INSERT INTO order_items (order_id, product_id, quantity, price) 
                                       VALUES ($order_id, {$item['product_id']}, {$item['quantity']}, {$item['price']})";
                    $conn->query($order_item_query);
                    
                    // Update product stock
                    $new_stock = $item['stock'] - $item['quantity'];
                    $update_stock = "UPDATE products SET stock = $new_stock WHERE id = {$item['product_id']}";
                    $conn->query($update_stock);
                }
                
                // Clear cart
                $clear_cart = "DELETE FROM cart WHERE user_id = $user_id";
                $conn->query($clear_cart);
                
                $conn->commit();
                
                // Redirect based on payment method
                if ($payment_method == 'esewa') {
                    // Redirect to eSewa payment
                    $_SESSION['order_id'] = $order_id;
                    $_SESSION['total_price'] = $total_price;
                    header('Location: esewa-payment.php');
                    exit();
                } else {
                    $_SESSION['message'] = "Order placed successfully! Your order ID is #" . str_pad($order_id, 6, '0', STR_PAD_LEFT);
                    $_SESSION['message_type'] = "success";
                    header('Location: order-success.php?id=' . $order_id);
                    exit();
                }
            } else {
                throw new Exception("Failed to create order");
            }
        } catch (Exception $e) {
            $conn->rollback();
            $errors[] = "Failed to place order. Please try again.";
        }
    }
}
?>

<div class="container">
    <div class="checkout-page">
        <div class="page-header">
            <h1>Checkout</h1>
            <p>Complete your order details</p>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <?php foreach ($errors as $error): ?>
                    <p><?php echo $error; ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="checkout-container">
            <div class="checkout-form">
                <form method="POST" action="">
                    <div class="form-section">
                        <h3>Shipping Information</h3>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="name">Full Name</label>
                                <input type="text" id="name" name="name" required 
                                       value="<?php echo htmlspecialchars($user['full_name']); ?>"
                                       pattern="[A-Za-z\s]+" title="Only alphabets allowed">
                            </div>
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" id="email" name="email" required 
                                       value="<?php echo htmlspecialchars($user['email']); ?>">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="phone">Phone Number</label>
                                <input type="tel" id="phone" name="phone" required maxlength="10"
                                       value="<?php echo htmlspecialchars($user['phone']); ?>"
                                       pattern="[0-9]{10}" title="Exactly 10 digits required">
                            </div>
                            <div class="form-group">
                                <label for="address">Delivery Address</label>
                                <textarea id="address" name="address" required rows="3"><?php echo htmlspecialchars($user['address']); ?></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h3>Payment Method</h3>
                        <div class="payment-methods">
                            <label class="payment-method">
                                <input type="radio" name="payment_method" value="cash_on_delivery" checked>
                                <div class="payment-info">
                                    <span class="payment-icon">💵</span>
                                    <div>
                                        <strong>Cash on Delivery</strong>
                                        <p>Pay when you receive your order</p>
                                    </div>
                                </div>
                            </label>
                            
                            <label class="payment-method">
                                <input type="radio" name="payment_method" value="esewa">
                                <div class="payment-info">
                                    <span class="payment-icon">💳</span>
                                    <div>
                                        <strong>eSewa</strong>
                                        <p>Pay securely with eSewa</p>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="form-actions">
                        <a href="cart.php" class="btn btn-outline">← Back to Cart</a>
                        <button type="submit" class="btn btn-primary">Place Order</button>
                    </div>
                </form>
            </div>

            <div class="order-summary">
                <h3>Order Summary</h3>
                <div class="summary-items">
                    <?php foreach ($cart_items as $item): ?>
                        <div class="summary-item">
                            <div class="item-info">
                                <img src="../assets/images/<?php echo htmlspecialchars($item['image']); ?>" 
                                     alt="<?php echo htmlspecialchars($item['name']); ?>"
                                     onerror="this.src='../assets/images/default-book.jpg'">
                                <div>
                                    <h4><?php echo htmlspecialchars($item['name']); ?></h4>
                                    <p>Qty: <?php echo $item['quantity']; ?> × ₹<?php echo number_format($item['price'], 2); ?></p>
                                </div>
                            </div>
                            <span class="item-total">₹<?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="summary-totals">
                    <div class="total-row">
                        <span>Subtotal</span>
                        <span>₹<?php echo number_format($total_price, 2); ?></span>
                    </div>
                    <div class="total-row">
                        <span>Shipping</span>
                        <span>Free</span>
                    </div>
                    <div class="total-row final">
                        <span>Total</span>
                        <span>₹<?php echo number_format($total_price, 2); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
