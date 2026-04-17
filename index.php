<?php
$page_title = "Home";
require_once 'includes/header.php';

// Handle add to cart
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_to_cart'])) {
    if (!is_logged_in()) {
        $_SESSION['message'] = "Please login to add items to cart.";
        $_SESSION['message_type'] = "error";
        header('Location: auth/login.php');
        exit();
    }

    $product_id = (int)$_POST['product_id'];
    $quantity = (int)$_POST['quantity'];
    $user_id = $_SESSION['user_id'];

    // Check if product exists and has enough stock
    $product_check = "SELECT stock FROM products WHERE id = $product_id";
    $product_result = $conn->query($product_check);
    
    if ($product_result && $product_result->num_rows > 0) {
        $product = $product_result->fetch_assoc();
        
        if ($product['stock'] >= $quantity) {
            // Check if item already in cart
            $cart_check = "SELECT id, quantity FROM cart WHERE user_id = $user_id AND product_id = $product_id";
            $cart_result = $conn->query($cart_check);
            
            if ($cart_result && $cart_result->num_rows > 0) {
                $cart_item = $cart_result->fetch_assoc();
                $new_quantity = $cart_item['quantity'] + $quantity;
                
                if ($product['stock'] >= $new_quantity) {
                    $update_cart = "UPDATE cart SET quantity = $new_quantity WHERE id = " . $cart_item['id'];
                    $conn->query($update_cart);
                    $_SESSION['message'] = "Cart updated successfully!";
                } else {
                    $_SESSION['message'] = "Not enough stock available!";
                }
            } else {
                $add_to_cart = "INSERT INTO cart (user_id, product_id, quantity) VALUES ($user_id, $product_id, $quantity)";
                $conn->query($add_to_cart);
                $_SESSION['message'] = "Product added to cart!";
            }
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Not enough stock available!";
            $_SESSION['message_type'] = "error";
        }
    }
    
    header('Location: index.php');
    exit();
}

// Build query for products
$where_clause = "WHERE 1=1";
$params = [];

// Filter by category
if (isset($_GET['category']) && !empty($_GET['category'])) {
    $category = clean_input($_GET['category']);
    $where_clause .= " AND category = '$category'";
}

// Search functionality
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = clean_input($_GET['search']);
    $where_clause .= " AND (name LIKE '%$search%' OR description LIKE '%$search%')";
}

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 8;
$offset = ($page - 1) * $per_page;

// Get total products count
$count_query = "SELECT COUNT(*) as total FROM products $where_clause";
$count_result = $conn->query($count_query);
$total_products = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_products / $per_page);

// Get products
$products_query = "SELECT * FROM products $where_clause ORDER BY created_at DESC LIMIT $per_page OFFSET $offset";
$products_result = $conn->query($products_query);
?>

<div class="hero-section">
    <div class="hero-content">
        <div class="hero-text">
            <h1>Welcome to BookNest</h1>
            <p>Discover your next favorite book from our curated collection of thousands of titles</p>
            <div class="hero-actions">
                <a href="#products" class="btn btn-primary btn-large">🛍️ Browse Books</a>
                <?php if (is_logged_in()): ?>
                    <a href="user/cart.php" class="btn btn-outline btn-large">🛒 View Cart</a>
                <?php else: ?>
                    <a href="auth/register.php" class="btn btn-outline btn-large">👤 Sign Up</a>
                <?php endif; ?>
                <a href="admin/login.php" class="btn btn-admin btn-large">🔐 Admin Access</a>
            </div>
        </div>
        <div class="hero-image">
            <div class="book-showcase">
                <div class="floating-book book-1">📚</div>
                <div class="floating-book book-2">📖</div>
                <div class="floating-book book-3">📘</div>
                <div class="floating-book book-4">📙</div>
            </div>
        </div>
    </div>
    <div class="hero-stats">
        <div class="stat">
            <span class="stat-number"><?php echo $total_products; ?></span>
            <span class="stat-label">Books Available</span>
        </div>
        <div class="stat">
            <span class="stat-number">1000+</span>
            <span class="stat-label">Happy Readers</span>
        </div>
        <div class="stat">
            <span class="stat-number">24/7</span>
            <span class="stat-label">Customer Support</span>
        </div>
        <div class="stat">
            <span class="stat-number">Free</span>
            <span class="stat-label">Delivery</span>
        </div>
    </div>
</div>

<div class="container">
    <div class="products-section" id="products">
        <div class="section-header">
            <h2>Our Books</h2>
            <div class="filters">
                <?php if (isset($_GET['category'])): ?>
                    <span class="active-filter">
                        Category: <?php echo htmlspecialchars($_GET['category']); ?>
                        <a href="index.php">✕</a>
                    </span>
                <?php endif; ?>
                <?php if (isset($_GET['search'])): ?>
                    <span class="active-filter">
                        Search: <?php echo htmlspecialchars($_GET['search']); ?>
                        <a href="index.php">✕</a>
                    </span>
                <?php endif; ?>
            </div>
        </div>

        <?php if ($products_result && $products_result->num_rows > 0): ?>
            <div class="products-grid">
                <?php while ($product = $products_result->fetch_assoc()): ?>
                    <div class="product-card">
                        <div class="product-image">
                            <img src="assets/images/<?php echo htmlspecialchars($product['image']); ?>" 
                                 alt="<?php echo htmlspecialchars($product['name']); ?>"
                                 onerror="this.src='assets/images/default-book.jpg'">
                            <?php if ($product['stock'] <= 5): ?>
                                <span class="stock-badge low-stock">Only <?php echo $product['stock']; ?> left</span>
                            <?php endif; ?>
                        </div>
                        <div class="product-info">
                            <span class="product-category"><?php echo htmlspecialchars($product['category']); ?></span>
                            <h3 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h3>
                            <p class="product-description"><?php echo htmlspecialchars(substr($product['description'], 0, 100)) . '...'; ?></p>
                            <div class="product-price">
                                <span class="price">₹<?php echo number_format($product['price'], 2); ?></span>
                                <?php if ($product['stock'] > 0): ?>
                                    <span class="stock in-stock">In Stock</span>
                                <?php else: ?>
                                    <span class="stock out-stock">Out of Stock</span>
                                <?php endif; ?>
                            </div>
                            <form method="POST" action="" class="add-to-cart-form">
                                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                <div class="quantity-selector">
                                    <label for="quantity_<?php echo $product['id']; ?>">Qty:</label>
                                    <select name="quantity" id="quantity_<?php echo $product['id']; ?>" 
                                            <?php echo $product['stock'] == 0 ? 'disabled' : ''; ?>>
                                        <?php for ($i = 1; $i <= min(10, $product['stock']); $i++): ?>
                                            <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                                <button type="submit" name="add_to_cart" 
                                        class="btn btn-primary <?php echo $product['stock'] == 0 ? 'disabled' : ''; ?>"
                                        <?php echo $product['stock'] == 0 ? 'disabled' : ''; ?>>
                                    <?php echo $product['stock'] == 0 ? 'Out of Stock' : 'Add to Cart'; ?>
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>

            <?php if ($total_pages > 1): ?>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?php echo $page - 1; ?><?php echo isset($_GET['category']) ? '&category=' . urlencode($_GET['category']) : ''; ?><?php echo isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''; ?>" 
                           class="page-link">« Previous</a>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <?php if ($i == $page): ?>
                            <span class="page-link active"><?php echo $i; ?></span>
                        <?php else: ?>
                            <a href="?page=<?php echo $i; ?><?php echo isset($_GET['category']) ? '&category=' . urlencode($_GET['category']) : ''; ?><?php echo isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''; ?>" 
                               class="page-link"><?php echo $i; ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>

                    <?php if ($page < $total_pages): ?>
                        <a href="?page=<?php echo $page + 1; ?><?php echo isset($_GET['category']) ? '&category=' . urlencode($_GET['category']) : ''; ?><?php echo isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''; ?>" 
                           class="page-link">Next »</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

        <?php else: ?>
            <div class="no-products">
                <h3>No books found</h3>
                <p>Try adjusting your search or browse our categories.</p>
                <a href="index.php" class="btn btn-primary">Browse All Books</a>
            </div>
        <?php endif; ?>
    </div>

    <div class="features-section">
        <div class="section-header">
            <h2>Why Choose BookNest?</h2>
            <p>We offer the best online book shopping experience in Nepal</p>
        </div>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">🚚</div>
                <h3>Fast Delivery</h3>
                <p>Get your books delivered within 2-3 business days across Kathmandu Valley</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">💰</div>
                <h3>Best Prices</h3>
                <p>Competitive prices on all your favorite books with regular discounts</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📚</div>
                <h3>Wide Selection</h3>
                <p>Thousands of books across multiple categories from fiction to education</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🔒</div>
                <h3>Secure Payment</h3>
                <p>Safe and secure payment options including eSewa, Khalti, and Cash on Delivery</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🔄</div>
                <h3>Easy Returns</h3>
                <p>7-day return policy if you're not satisfied with your purchase</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📱</div>
                <h3>Mobile Friendly</h3>
                <p>Shop seamlessly from your phone, tablet, or computer</p>
            </div>
        </div>
    </div>
    
    <!-- New Categories Section -->
    <div class="categories-section">
        <div class="section-header">
            <h2>Shop by Category</h2>
            <p>Find books in your favorite genre</p>
        </div>
        <div class="categories-grid">
            <?php 
            $featured_categories = ['Fiction', 'Science Fiction', 'Education', 'Romance', 'Mystery', 'Self Help'];
            foreach ($featured_categories as $category): 
                $count_query = "SELECT COUNT(*) as count FROM products WHERE category = '$category'";
                $count_result = $conn->query($count_query);
                $count = $count_result ? $count_result->fetch_assoc()['count'] : 0;
            ?>
                <div class="category-card">
                    <div class="category-icon">
                        <?php 
                        $icons = ['Fiction' => '📖', 'Science Fiction' => '🚀', 'Education' => '🎓', 'Romance' => '💕', 'Mystery' => '🔍', 'Self Help' => '💪'];
                        echo $icons[$category] ?? '📚';
                        ?>
                    </div>
                    <h3><?php echo htmlspecialchars($category); ?></h3>
                    <p><?php echo $count; ?> books available</p>
                    <a href="index.php?category=<?php echo urlencode($category); ?>" class="btn btn-sm btn-outline">Browse</a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
