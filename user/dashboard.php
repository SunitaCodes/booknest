<?php
$page_title = "My Account";
require_once '../includes/header.php';
require_once '../includes/auth.php';
require_user();

$user_id = $_SESSION['user_id'];

// Get user information
$user_query = "SELECT * FROM users WHERE id = $user_id";
$user_result = $conn->query($user_query);
$user = $user_result->fetch_assoc();

// Get recent orders
$orders_query = "SELECT o.*, COUNT(oi.id) as item_count FROM orders o 
                 LEFT JOIN order_items oi ON o.id = oi.order_id 
                 WHERE o.user_id = $user_id 
                 GROUP BY o.id 
                 ORDER BY o.created_at DESC 
                 LIMIT 5";
$orders_result = $conn->query($orders_query);

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $full_name = clean_input($_POST['full_name']);
    $phone = clean_input($_POST['phone']);
    $address = clean_input($_POST['address']);
    
    $errors = [];
    
    if (empty($full_name) || !preg_match("/^[a-zA-Z\s]+$/", $full_name)) {
        $errors[] = "Full name should contain only alphabets";
    }
    
    if (empty($phone) || !preg_match("/^[0-9]{10}$/", $phone)) {
        $errors[] = "Phone number must be exactly 10 digits";
    }
    
    if (empty($address)) {
        $errors[] = "Address is required";
    }
    
    if (empty($errors)) {
        $update_query = "UPDATE users SET full_name = '$full_name', phone = '$phone', address = '$address' WHERE id = $user_id";
        if ($conn->query($update_query)) {
            $_SESSION['user_name'] = $full_name;
            $_SESSION['message'] = "Profile updated successfully!";
            $_SESSION['message_type'] = "success";
            header('Location: dashboard.php');
            exit();
        }
    }
}

// Handle password change
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    $errors = [];
    
    if (empty($current_password)) {
        $errors[] = "Current password is required";
    }
    
    if (empty($new_password) || strlen($new_password) < 8 || !preg_match("/^(?=.*[A-Za-z])(?=.*\d).+$/", $new_password)) {
        $errors[] = "New password must be at least 8 characters with letters and numbers";
    }
    
    if ($new_password !== $confirm_password) {
        $errors[] = "Passwords do not match";
    }
    
    if (empty($errors)) {
        if (password_verify($current_password, $user['password'])) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $update_query = "UPDATE users SET password = '$hashed_password' WHERE id = $user_id";
            if ($conn->query($update_query)) {
                $_SESSION['message'] = "Password changed successfully!";
                $_SESSION['message_type'] = "success";
                header('Location: dashboard.php');
                exit();
            }
        } else {
            $errors[] = "Current password is incorrect";
        }
    }
}
?>

<div class="user-dashboard">
    <div class="dashboard-sidebar">
        <div class="user-profile-summary">
            <div class="user-avatar">
                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($user['full_name']); ?>&background=3498db&color=fff&size=80" alt="Profile">
            </div>
            <h3><?php echo htmlspecialchars($user['full_name']); ?></h3>
            <p><?php echo htmlspecialchars($user['email']); ?></p>
        </div>
        
        <nav class="dashboard-nav">
            <ul class="dashboard-nav-links">
                <li><a href="#profile" class="nav-link active" onclick="showSection('profile')">👤 Profile</a></li>
                <li><a href="#orders" class="nav-link" onclick="showSection('orders')">📦 My Orders</a></li>
                <li><a href="#cart" class="nav-link" onclick="showSection('cart')">🛒 Shopping Cart</a></li>
                <li><a href="#wishlist" class="nav-link" onclick="showSection('wishlist')">❤️ Wishlist</a></li>
                <li><a href="#settings" class="nav-link" onclick="showSection('settings')">⚙️ Settings</a></li>
                <li><a href="#password" class="nav-link" onclick="showSection('password')">🔒 Password</a></li>
            </ul>
        </nav>
        
        <div class="quick-actions">
            <a href="../index.php" class="btn btn-primary btn-block">🛍️ Continue Shopping</a>
        </div>
    </div>
    
    <div class="dashboard-main">
        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <?php foreach ($errors as $error): ?>
                    <p><?php echo $error; ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <!-- Profile Section -->
        <section id="profile-section" class="dashboard-section active">
            <div class="section-header">
                <h2>Profile Information</h2>
                <p>Manage your personal information and contact details</p>
            </div>
            
            <div class="card">
                <form method="POST" action="" class="profile-form">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="full_name">Full Name</label>
                            <input type="text" id="full_name" name="full_name" required 
                                   value="<?php echo htmlspecialchars($user['full_name']); ?>"
                                   pattern="[A-Za-z\s]+" title="Only alphabets allowed">
                        </div>
                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" readonly>
                            <small>Email cannot be changed. Contact support if needed.</small>
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
                    <button type="submit" name="update_profile" class="btn btn-primary">Update Profile</button>
                </form>
            </div>
        </section>
        
        <!-- Orders Section -->
        <section id="orders-section" class="dashboard-section">
            <div class="section-header">
                <h2>Recent Orders</h2>
                <p>Track and manage your orders</p>
            </div>
            
            <div class="card">
                <?php if ($orders_result && $orders_result->num_rows > 0): ?>
                    <div class="orders-grid">
                        <?php while ($order = $orders_result->fetch_assoc()): ?>
                            <div class="order-card">
                                <div class="order-header">
                                    <div>
                                        <strong>Order #<?php echo str_pad($order['id'], 6, '0', STR_PAD_LEFT); ?></strong>
                                        <span class="order-date"><?php echo date('M d, Y', strtotime($order['created_at'])); ?></span>
                                    </div>
                                    <span class="order-status status-<?php echo $order['status']; ?>">
                                        <?php echo ucfirst($order['status']); ?>
                                    </span>
                                </div>
                                <div class="order-details">
                                    <div class="order-info">
                                        <p><strong>Items:</strong> <?php echo $order['item_count']; ?></p>
                                        <p><strong>Total:</strong> ₹<?php echo number_format($order['total_price'], 2); ?></p>
                                        <p><strong>Payment:</strong> <?php echo ucfirst(str_replace('_', ' ', $order['payment_method'])); ?></p>
                                    </div>
                                    <div class="order-actions">
                                        <a href="order-details.php?id=<?php echo $order['id']; ?>" class="btn btn-sm btn-outline">View Details</a>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                    <div class="view-all-orders">
                        <a href="orders.php" class="btn btn-secondary">View All Orders</a>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <div class="empty-icon">📦</div>
                        <h3>No orders yet</h3>
                        <p>You haven't placed any orders. Start shopping to see your orders here.</p>
                        <a href="../index.php" class="btn btn-primary">Start Shopping</a>
                    </div>
                <?php endif; ?>
            </div>
        </section>
        
        <!-- Cart Section -->
        <section id="cart-section" class="dashboard-section">
            <div class="section-header">
                <h2>Shopping Cart</h2>
                <p>Items in your cart</p>
            </div>
            <div class="card">
                <p><a href="cart.php">View your shopping cart</a> to manage items and proceed to checkout.</p>
            </div>
        </section>
        
        <!-- Wishlist Section -->
        <section id="wishlist-section" class="dashboard-section">
            <div class="section-header">
                <h2>My Wishlist</h2>
                <p>Books you've saved for later</p>
            </div>
            <div class="card">
                <div class="empty-state">
                    <div class="empty-icon">❤️</div>
                    <h3>Your wishlist is empty</h3>
                    <p>Save books you're interested in for later purchase.</p>
                    <a href="../index.php" class="btn btn-primary">Browse Books</a>
                </div>
            </div>
        </section>
        
        <!-- Settings Section -->
        <section id="settings-section" class="dashboard-section">
            <div class="section-header">
                <h2>Account Settings</h2>
                <p>Manage your account preferences</p>
            </div>
            <div class="card">
                <p>Account settings and preferences will be available here.</p>
            </div>
        </section>
        
        <!-- Password Section -->
        <section id="password-section" class="dashboard-section">
            <div class="section-header">
                <h2>Change Password</h2>
                <p>Update your account password</p>
            </div>
            
            <div class="card">
                <form method="POST" action="" class="password-form">
                    <div class="form-group">
                        <label for="current_password">Current Password</label>
                        <input type="password" id="current_password" name="current_password" required>
                    </div>
                    <div class="form-group">
                        <label for="new_password">New Password</label>
                        <input type="password" id="new_password" name="new_password" required 
                               minlength="8" title="Minimum 8 characters with letters and numbers">
                        <small>Must be at least 8 characters with letters and numbers</small>
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Confirm New Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" required>
                    </div>
                    <button type="submit" name="change_password" class="btn btn-primary">Change Password</button>
                </form>
            </div>
        </section>
    </div>
</div>

<style>
.user-dashboard {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
    display: grid;
    grid-template-columns: 280px 1fr;
    gap: 30px;
}

.dashboard-sidebar {
    background: white;
    border-radius: 10px;
    padding: 25px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    height: fit-content;
    position: sticky;
    top: 20px;
}

.user-profile-summary {
    text-align: center;
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 1px solid #eee;
}

.user-avatar img {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    margin-bottom: 15px;
}

.user-profile-summary h3 {
    margin: 0 0 5px 0;
    color: #333;
}

.user-profile-summary p {
    margin: 0;
    color: #666;
    font-size: 0.9em;
}

.dashboard-nav-links {
    list-style: none;
    margin: 0 0 25px 0;
    padding: 0;
}

.dashboard-nav-links li {
    margin-bottom: 5px;
}

.dashboard-nav-links .nav-link {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 15px;
    color: #555;
    text-decoration: none;
    border-radius: 8px;
    transition: all 0.3s;
}

.dashboard-nav-links .nav-link:hover {
    background: #f8f9fa;
    color: #333;
}

.dashboard-nav-links .nav-link.active {
    background: #3498db;
    color: white;
}

.btn-block {
    display: block;
    width: 100%;
    text-align: center;
}

.dashboard-main {
    min-height: 600px;
}

.dashboard-section {
    display: none;
}

.dashboard-section.active {
    display: block;
}

.section-header {
    margin-bottom: 25px;
}

.section-header h2 {
    margin: 0 0 5px 0;
    color: #333;
}

.section-header p {
    margin: 0;
    color: #666;
}

.card {
    background: white;
    border-radius: 10px;
    padding: 25px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    margin-bottom: 20px;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-bottom: 20px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: 500;
    color: #333;
}

.form-group input,
.form-group textarea {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 14px;
}

.form-group input:focus,
.form-group textarea:focus {
    outline: none;
    border-color: #3498db;
}

.form-group small {
    display: block;
    margin-top: 5px;
    color: #666;
    font-size: 0.85em;
}

.orders-grid {
    display: grid;
    gap: 15px;
}

.order-card {
    border: 1px solid #eee;
    border-radius: 8px;
    padding: 15px;
}

.order-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}

.order-date {
    display: block;
    color: #666;
    font-size: 0.9em;
    margin-top: 2px;
}

.order-details {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.order-info p {
    margin: 0 0 5px 0;
    color: #555;
}

.order-status {
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 0.8em;
    font-weight: 600;
}

.status-pending { background: #fff3cd; color: #856404; }
.status-processing { background: #cce5ff; color: #004085; }
.status-shipped { background: #d1ecf1; color: #0c5460; }
.status-delivered { background: #d4edda; color: #155724; }
.status-cancelled { background: #f8d7da; color: #721c24; }

.empty-state {
    text-align: center;
    padding: 40px 20px;
}

.empty-icon {
    font-size: 3em;
    margin-bottom: 15px;
}

.empty-state h3 {
    margin: 0 0 10px 0;
    color: #333;
}

.empty-state p {
    margin: 0 0 20px 0;
    color: #666;
}

.view-all-orders {
    text-align: center;
    margin-top: 20px;
}

@media (max-width: 768px) {
    .user-dashboard {
        grid-template-columns: 1fr;
    }
    
    .dashboard-sidebar {
        position: static;
    }
    
    .form-row {
        grid-template-columns: 1fr;
    }
    
    .order-details {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
    }
}
</style>

<script>
function showSection(sectionName) {
    // Hide all sections
    const sections = document.querySelectorAll('.dashboard-section');
    sections.forEach(section => section.classList.remove('active'));
    
    // Remove active class from all nav links
    const navLinks = document.querySelectorAll('.dashboard-nav-links .nav-link');
    navLinks.forEach(link => link.classList.remove('active'));
    
    // Show selected section
    document.getElementById(sectionName + '-section').classList.add('active');
    
    // Add active class to clicked nav link
    document.querySelector(`[onclick="showSection('${sectionName}')"]`).classList.add('active');
}
</script>

<?php require_once '../includes/footer.php'; ?>
