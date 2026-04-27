<?php
$page_title = "Manage Products";
require_once '../includes/auth.php';
require_admin();

// Handle product actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_product'])) {
        $name = clean_input($_POST['name']);
        $category = clean_input($_POST['category']);
        $price = (float)$_POST['price'];
        $stock = (int)$_POST['stock'];
        $description = clean_input($_POST['description']);
        
        // Handle image upload
        $image = 'default-book.jpg';
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            $filename = $_FILES['image']['name'];
            $filetype = pathinfo($filename, PATHINFO_EXTENSION);
            
            if (in_array(strtolower($filetype), $allowed)) {
                $newname = time() . '_' . $filename;
                $upload_path = '../assets/images/' . $newname;
                
                if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                    $image = $newname;
                }
            }
        }
        
        $insert_query = "INSERT INTO products (name, category, price, stock, image, description) 
                        VALUES ('$name', '$category', $price, $stock, '$image', '$description')";
        
        if ($conn->query($insert_query)) {
            $_SESSION['message'] = "Product added successfully!";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Failed to add product!";
            $_SESSION['message_type'] = "error";
        }
        header('Location: manage_products.php');
        exit();
    }

    if (isset($_POST['update_product'])) {
        $id = (int)$_POST['id'];
        $name = clean_input($_POST['name']);
        $category = clean_input($_POST['category']);
        $price = (float)$_POST['price'];
        $stock = (int)$_POST['stock'];
        $description = clean_input($_POST['description']);
        
        // Handle image upload
        $update_fields = "name = '$name', category = '$category', price = $price, stock = $stock, description = '$description'";
        
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            $filename = $_FILES['image']['name'];
            $filetype = pathinfo($filename, PATHINFO_EXTENSION);
            
            if (in_array(strtolower($filetype), $allowed)) {
                $newname = time() . '_' . $filename;
                $upload_path = '../assets/images/' . $newname;
                
                if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                    $update_fields .= ", image = '$newname'";
                }
            }
        }
        
        $update_query = "UPDATE products SET $update_fields WHERE id = $id";
        
        if ($conn->query($update_query)) {
            $_SESSION['message'] = "Product updated successfully!";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Failed to update product!";
            $_SESSION['message_type'] = "error";
        }
        header('Location: manage_products.php');
        exit();
    }

    if (isset($_POST['delete_product'])) {
        $id = (int)$_POST['id'];
        $delete_query = "DELETE FROM products WHERE id = $id";
        
        if ($conn->query($delete_query)) {
            $_SESSION['message'] = "Product deleted successfully!";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Failed to delete product!";
            $_SESSION['message_type'] = "error";
        }
        header('Location: manage_products.php');
        exit();
    }
}

// Get products with filters
$where_clause = "WHERE 1=1";

if (isset($_GET['filter']) && $_GET['filter'] == 'low_stock') {
    $where_clause .= " AND stock <= 5";
}

if (isset($_GET['category']) && !empty($_GET['category'])) {
    $category = clean_input($_GET['category']);
    $where_clause .= " AND category = '$category'";
}

if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = clean_input($_GET['search']);
    $where_clause .= " AND (name LIKE '%$search%' OR description LIKE '%$search%')";
}

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 10;
$offset = ($page - 1) * $per_page;

$count_query = "SELECT COUNT(*) as total FROM products $where_clause";
$count_result = $conn->query($count_query);
$total_products = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_products / $per_page);

$products_query = "SELECT * FROM products $where_clause ORDER BY created_at DESC LIMIT $per_page OFFSET $offset";
$products_result = $conn->query($products_query);

// Get categories for filter
$categories_query = "SELECT DISTINCT category FROM products ORDER BY category";
$categories_result = $conn->query($categories_query);

require_once 'includes/admin-header.php';
?>

<div class="admin-products">
    <div class="page-header">
        <h1>Manage Products</h1>
        <button class="btn btn-primary" onclick="showAddProductModal()">Add New Product</button>
    </div>

    <div class="filters-section">
        <form method="GET" class="filters-form">
            <input type="text" name="search" placeholder="Search products..." 
                   value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
            
            <select name="category">
                <option value="">All Categories</option>
                <?php if ($categories_result): ?>
                    <?php while ($cat = $categories_result->fetch_assoc()): ?>
                        <option value="<?php echo $cat['category']; ?>" 
                                <?php echo (isset($_GET['category']) && $_GET['category'] == $cat['category']) ? 'selected' : ''; ?>>
                            <?php echo $cat['category']; ?>
                        </option>
                    <?php endwhile; ?>
                <?php endif; ?>
            </select>
            
            <button type="submit" class="btn btn-secondary">Filter</button>
            <a href="manage_products.php" class="btn btn-outline">Clear</a>
        </form>
        
        <div class="quick-filters">
            <a href="?filter=low_stock" class="filter-badge <?php echo (isset($_GET['filter']) && $_GET['filter'] == 'low_stock') ? 'active' : ''; ?>">
                ⚠️ Low Stock (≤5)
            </a>
        </div>
    </div>

    <div class="products-table">
        <table>
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($products_result && $products_result->num_rows > 0): ?>
                    <?php while ($product = $products_result->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <img src="../assets/images/<?php echo htmlspecialchars($product['image']); ?>" 
                                     alt="<?php echo htmlspecialchars($product['name']); ?>"
                                     style="width: 50px; height: 70px; object-fit: cover; border-radius: 5px;"
                                     onerror="this.src='../assets/images/default-book.jpg'">
                            </td>
                            <td>
                                <strong><?php echo htmlspecialchars($product['name']); ?></strong>
                                <br>
                                <small><?php echo htmlspecialchars(substr($product['description'], 0, 50)); ?>...</small>
                            </td>
                            <td><?php echo htmlspecialchars($product['category']); ?></td>
                            <td>₹<?php echo number_format($product['price'], 2); ?></td>
                            <td>
                                <?php echo $product['stock']; ?>
                                <?php if ($product['stock'] <= 5): ?>
                                    <span class="stock-warning">⚠️</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($product['stock'] > 0): ?>
                                    <span class="status-badge in-stock">In Stock</span>
                                <?php else: ?>
                                    <span class="status-badge out-stock">Out of Stock</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-outline" onclick="editProduct(<?php echo $product['id']; ?>)">Edit</button>
                                <button class="btn btn-sm btn-danger" onclick="deleteProduct(<?php echo $product['id']; ?>)">Delete</button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="no-data">No products found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if ($total_pages > 1): ?>
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?page=<?php echo $page - 1; ?><?php echo isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''; ?><?php echo isset($_GET['category']) ? '&category=' . urlencode($_GET['category']) : ''; ?>" 
                   class="page-link">« Previous</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <?php if ($i == $page): ?>
                    <span class="page-link active"><?php echo $i; ?></span>
                <?php else: ?>
                    <a href="?page=<?php echo $i; ?><?php echo isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''; ?><?php echo isset($_GET['category']) ? '&category=' . urlencode($_GET['category']) : ''; ?>" 
                       class="page-link"><?php echo $i; ?></a>
                <?php endif; ?>
            <?php endfor; ?>

            <?php if ($page < $total_pages): ?>
                <a href="?page=<?php echo $page + 1; ?><?php echo isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''; ?><?php echo isset($_GET['category']) ? '&category=' . urlencode($_GET['category']) : ''; ?>" 
                   class="page-link">Next »</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Add Product Modal -->
<div id="addProductModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Add New Product</h3>
            <button class="close-btn" onclick="closeModal('addProductModal')">&times;</button>
        </div>
        <form method="POST" action="" enctype="multipart/form-data" class="product-form">
            <div class="form-row">
                <div class="form-group">
                    <label for="name">Product Name</label>
                    <input type="text" id="name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="category">Category</label>
                    <input type="text" id="category" name="category" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="price">Price (₹)</label>
                    <input type="number" id="price" name="price" step="0.01" min="0" required>
                </div>
                <div class="form-group">
                    <label for="stock">Stock Quantity</label>
                    <input type="number" id="stock" name="stock" min="0" required>
                </div>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="3" required></textarea>
            </div>
            <div class="form-group">
                <label for="image">Product Image</label>
                <input type="file" id="image" name="image" accept="image/*">
                <small>Leave empty to use default image</small>
            </div>
            <div class="form-actions">
                <button type="button" class="btn btn-outline" onclick="closeModal('addProductModal')">Cancel</button>
                <button type="submit" name="add_product" class="btn btn-primary">Add Product</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Product Modal -->
<div id="editProductModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Edit Product</h3>
            <button class="close-btn" onclick="closeModal('editProductModal')">&times;</button>
        </div>
        <form method="POST" action="" enctype="multipart/form-data" class="product-form">
            <input type="hidden" id="edit_id" name="id">
            <div class="form-row">
                <div class="form-group">
                    <label for="edit_name">Product Name</label>
                    <input type="text" id="edit_name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="edit_category">Category</label>
                    <input type="text" id="edit_category" name="category" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="edit_price">Price (₹)</label>
                    <input type="number" id="edit_price" name="price" step="0.01" min="0" required>
                </div>
                <div class="form-group">
                    <label for="edit_stock">Stock Quantity</label>
                    <input type="number" id="edit_stock" name="stock" min="0" required>
                </div>
            </div>
            <div class="form-group">
                <label for="edit_description">Description</label>
                <textarea id="edit_description" name="description" rows="3" required></textarea>
            </div>
            <div class="form-group">
                <label for="edit_image">Product Image</label>
                <input type="file" id="edit_image" name="image" accept="image/*">
                <small>Leave empty to keep current image</small>
            </div>
            <div class="form-actions">
                <button type="button" class="btn btn-outline" onclick="closeModal('editProductModal')">Cancel</button>
                <button type="submit" name="update_product" class="btn btn-primary">Update Product</button>
            </div>
        </form>
    </div>
</div>

<style>
.admin-products {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
}

.filters-section {
    background: white;
    padding: 20px;
    border-radius: 10px;
    margin-bottom: 20px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.filters-form {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    align-items: center;
}

.filters-form input,
.filters-form select {
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 5px;
}

.quick-filters {
    margin-top: 15px;
}

.filter-badge {
    display: inline-block;
    padding: 5px 10px;
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 15px;
    text-decoration: none;
    color: #666;
    font-size: 0.9em;
}

.filter-badge.active {
    background: #007bff;
    color: white;
    border-color: #007bff;
}

.products-table {
    background: white;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.products-table table {
    width: 100%;
    border-collapse: collapse;
}

.products-table th,
.products-table td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #dee2e6;
}

.products-table th {
    background: #f8f9fa;
    font-weight: 600;
}

.stock-warning {
    color: #ffc107;
    font-size: 1.2em;
}

.status-badge {
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 0.8em;
    font-weight: 600;
}

.in-stock {
    background: #d4edda;
    color: #155724;
}

.out-stock {
    background: #f8d7da;
    color: #721c24;
}

.no-data {
    text-align: center;
    color: #666;
    padding: 40px;
}

.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
}

.modal-content {
    background-color: white;
    margin: 5% auto;
    padding: 0;
    border-radius: 10px;
    width: 90%;
    max-width: 600px;
    max-height: 90vh;
    overflow-y: auto;
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px;
    border-bottom: 1px solid #dee2e6;
}

.modal-header h3 {
    margin: 0;
}

.close-btn {
    background: none;
    border: none;
    font-size: 1.5em;
    cursor: pointer;
    color: #666;
}

.product-form {
    padding: 20px;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
    margin-bottom: 15px;
}

.form-group {
    margin-bottom: 15px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: 500;
}

.form-group input,
.form-group textarea,
.form-group select {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 14px;
}

.form-group small {
    display: block;
    margin-top: 5px;
    color: #666;
    font-size: 0.8em;
}

.form-actions {
    display: flex;
    gap: 10px;
    justify-content: flex-end;
    margin-top: 20px;
}

@media (max-width: 768px) {
    .page-header {
        flex-direction: column;
        gap: 15px;
    }
    
    .filters-form {
        flex-direction: column;
        align-items: stretch;
    }
    
    .form-row {
        grid-template-columns: 1fr;
    }
    
    .products-table {
        overflow-x: auto;
    }
}
</style>

<script>
function showAddProductModal() {
    document.getElementById('addProductModal').style.display = 'block';
}

function editProduct(id) {
    // Fetch product data via AJAX or use data attributes
    // For simplicity, we'll make an AJAX call
    fetch('get_product.php?id=' + id)
        .then(response => response.json())
        .then(data => {
            document.getElementById('edit_id').value = data.id;
            document.getElementById('edit_name').value = data.name;
            document.getElementById('edit_category').value = data.category;
            document.getElementById('edit_price').value = data.price;
            document.getElementById('edit_stock').value = data.stock;
            document.getElementById('edit_description').value = data.description;
            document.getElementById('editProductModal').style.display = 'block';
        });
}

function deleteProduct(id) {
    if (confirm('Are you sure you want to delete this product?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = '<input type="hidden" name="id" value="' + id + '"><input type="hidden" name="delete_product" value="1">';
        document.body.appendChild(form);
        form.submit();
    }
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

// Close modal when clicking outside
window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.style.display = 'none';
    }
}
</script>

<?php require_once 'includes/admin-footer.php'; ?>
