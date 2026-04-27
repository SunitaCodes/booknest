<?php
$page_title = "Shopping Cart";
require_once '../includes/auth.php';
require_login();
require_user();

$user_id = $_SESSION['user_id'];

// Handle cart updates
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_cart'])) {
        foreach ($_POST['quantities'] as $cart_id => $quantity) {
            $quantity = (int)$quantity;
            if ($quantity > 0) {
                // Check stock availability
                $stock_check = "SELECT p.stock FROM cart c JOIN products p ON c.product_id = p.id WHERE c.id = $cart_id AND c.user_id = $user_id";
                $stock_result = $conn->query($stock_check);
                if ($stock_result && $stock_result->num_rows > 0) {
                    $stock = $stock_result->fetch_assoc()['stock'];
                    if ($quantity <= $stock) {
                        $update_query = "UPDATE cart SET quantity = $quantity WHERE id = $cart_id AND user_id = $user_id";
                        $conn->query($update_query);
                    } else {
                        $_SESSION['message'] = "Not enough stock for some items!";
                        $_SESSION['message_type'] = "error";
                    }
                }
            } else {
                // Remove item if quantity is 0
                $delete_query = "DELETE FROM cart WHERE id = $cart_id AND user_id = $user_id";
                $conn->query($delete_query);
            }
        }
        $_SESSION['message'] = "Cart updated successfully!";
        $_SESSION['message_type'] = "success";
        header('Location: cart.php');
        exit();
    }

    if (isset($_POST['remove_item'])) {
        $cart_id = (int)$_POST['remove_item'];
        $delete_query = "DELETE FROM cart WHERE id = $cart_id AND user_id = $user_id";
        $conn->query($delete_query);
        $_SESSION['message'] = "Item removed from cart!";
        $_SESSION['message_type'] = "success";
        header('Location: cart.php');
        exit();
    }

    if (isset($_POST['clear_cart'])) {
        $clear_query = "DELETE FROM cart WHERE user_id = $user_id";
        $conn->query($clear_query);
        $_SESSION['message'] = "Cart cleared!";
        $_SESSION['message_type'] = "success";
        header('Location: cart.php');
        exit();
    }
}

// Get cart items
$cart_query = "SELECT c.id as cart_id, c.quantity, p.* FROM cart c 
               JOIN products p ON c.product_id = p.id 
               WHERE c.user_id = $user_id";
$cart_result = $conn->query($cart_query);

$cart_items = [];
$total_price = 0;
$total_items = 0;

if ($cart_result && $cart_result->num_rows > 0) {
    while ($item = $cart_result->fetch_assoc()) {
        $cart_items[] = $item;
        $total_price += $item['price'] * $item['quantity'];
        $total_items += $item['quantity'];
    }
}

require_once '../includes/header.php';
?>

<div class="container">
    <div class="cart-page">
        <div class="page-header">
            <h1>Shopping Cart</h1>
            <p>You have <?php echo $total_items; ?> item<?php echo $total_items != 1 ? 's' : ''; ?> in your cart</p>
        </div>

        <?php if (!empty($cart_items)): ?>
            <form method="POST" action="" class="cart-form">
                <div class="cart-items">
                    <div class="cart-header">
                        <div class="header-product">Product</div>
                        <div class="header-price">Price</div>
                        <div class="header-quantity">Quantity</div>
                        <div class="header-total">Total</div>
                        <div class="header-actions">Actions</div>
                    </div>

                    <?php foreach ($cart_items as $item): ?>
                        <div class="cart-item">
                            <div class="item-product">
                                <img src="../assets/images/<?php echo htmlspecialchars($item['image']); ?>" 
                                     alt="<?php echo htmlspecialchars($item['name']); ?>"
                                     onerror="this.src='../assets/images/default-book.jpg'">
                                <div class="item-details">
                                    <h4><?php echo htmlspecialchars($item['name']); ?></h4>
                                    <span class="item-category"><?php echo htmlspecialchars($item['category']); ?></span>
                                    <?php if ($item['stock'] <= 5): ?>
                                        <span class="stock-warning">Only <?php echo $item['stock']; ?> left in stock</span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="item-price">
                                ₹<?php echo number_format($item['price'], 2); ?>
                            </div>

                            <div class="item-quantity">
                                <input type="number" name="quantities[<?php echo $item['cart_id']; ?>]" 
                                       value="<?php echo $item['quantity']; ?>" 
                                       min="1" max="<?php echo $item['stock']; ?>" 
                                       class="quantity-input">
                            </div>

                            <div class="item-total">
                                ₹<?php echo number_format($item['price'] * $item['quantity'], 2); ?>
                            </div>

                            <div class="item-actions">
                                <button type="submit" name="remove_item" value="<?php echo $item['cart_id']; ?>" class="btn btn-danger btn-sm">Remove</button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="cart-summary">
                    <div class="summary-content">
                        <h3>Order Summary</h3>
                        <div class="summary-row">
                            <span>Subtotal (<?php echo $total_items; ?> items)</span>
                            <span>₹<?php echo number_format($total_price, 2); ?></span>
                        </div>
                        <div class="summary-row">
                            <span>Shipping</span>
                            <span>Free</span>
                        </div>
                        <div class="summary-row total">
                            <span>Total</span>
                            <span>₹<?php echo number_format($total_price, 2); ?></span>
                        </div>

                        <div class="cart-actions">
                            <button type="submit" name="update_cart" class="btn btn-secondary">Update Cart</button>
                            <a href="checkout.php" class="btn btn-primary">Proceed to Checkout</a>
                        </div>

                        <button type="submit" name="clear_cart" class="btn btn-outline btn-sm cart-clear-btn">Clear Cart</button>

                        <div class="continue-shopping">
                            <a href="../index.php">← Continue Shopping</a>
                        </div>
                    </div>
                </div>
            </form>

        <?php else: ?>
            <div class="empty-cart">
                <div class="empty-cart-icon">🛒</div>
                <h3>Your cart is empty</h3>
                <p>Looks like you haven't added any books to your cart yet.</p>
                <a href="../index.php" class="btn btn-primary">Start Shopping</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
