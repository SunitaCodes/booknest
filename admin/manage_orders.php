<?php
$page_title = "Manage Orders";
require_once 'includes/admin-header.php';

// Handle order status update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $order_id = (int)$_POST['order_id'];
    $status = clean_input($_POST['status']);
    
    if (in_array($status, ['pending', 'processing', 'shipped', 'delivered', 'cancelled'])) {
        $update_query = "UPDATE orders SET status = '$status' WHERE id = $order_id";
        
        if ($conn->query($update_query)) {
            $_SESSION['message'] = "Order status updated successfully!";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Failed to update order status!";
            $_SESSION['message_type'] = "error";
        }
    }
    header('Location: manage_orders.php');
    exit();
}

// Get orders with filters
$where_clause = "WHERE 1=1";

if (isset($_GET['status']) && !empty($_GET['status'])) {
    $status = clean_input($_GET['status']);
    $where_clause .= " AND status = '$status'";
}

if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = clean_input($_GET['search']);
    $where_clause .= " AND (o.id LIKE '%$search%' OR u.full_name LIKE '%$search%' OR u.email LIKE '%$search%')";
}

if (isset($_GET['date_from']) && !empty($_GET['date_from'])) {
    $date_from = clean_input($_GET['date_from']);
    $where_clause .= " AND DATE(o.created_at) >= '$date_from'";
}

if (isset($_GET['date_to']) && !empty($_GET['date_to'])) {
    $date_to = clean_input($_GET['date_to']);
    $where_clause .= " AND DATE(o.created_at) <= '$date_to'";
}

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 15;
$offset = ($page - 1) * $per_page;

$count_query = "SELECT COUNT(*) as total FROM orders o $where_clause";
$count_result = $conn->query($count_query);
$total_orders = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_orders / $per_page);

$orders_query = "SELECT o.*, u.full_name, u.email, COUNT(oi.id) as item_count 
                 FROM orders o 
                 JOIN users u ON o.user_id = u.id 
                 LEFT JOIN order_items oi ON o.id = oi.order_id 
                 $where_clause 
                 GROUP BY o.id 
                 ORDER BY o.created_at DESC 
                 LIMIT $per_page OFFSET $offset";
$orders_result = $conn->query($orders_query);
?>

<div class="admin-orders">
    <div class="page-header">
        <h1>Manage Orders</h1>
        <div class="header-stats">
            <span class="stat-badge pending-count">Pending: <?php 
                $pending_query = "SELECT COUNT(*) as count FROM orders WHERE status = 'pending'";
                echo $conn->query($pending_query)->fetch_assoc()['count']; 
            ?></span>
            <span class="stat-badge processing-count">Processing: <?php 
                $processing_query = "SELECT COUNT(*) as count FROM orders WHERE status = 'processing'";
                echo $conn->query($processing_query)->fetch_assoc()['count']; 
            ?></span>
        </div>
    </div>

    <div class="filters-section">
        <form method="GET" class="filters-form">
            <input type="text" name="search" placeholder="Search by Order ID, Customer Name, or Email..." 
                   value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
            
            <select name="status">
                <option value="">All Status</option>
                <option value="pending" <?php echo (isset($_GET['status']) && $_GET['status'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
                <option value="processing" <?php echo (isset($_GET['status']) && $_GET['status'] == 'processing') ? 'selected' : ''; ?>>Processing</option>
                <option value="shipped" <?php echo (isset($_GET['status']) && $_GET['status'] == 'shipped') ? 'selected' : ''; ?>>Shipped</option>
                <option value="delivered" <?php echo (isset($_GET['status']) && $_GET['status'] == 'delivered') ? 'selected' : ''; ?>>Delivered</option>
                <option value="cancelled" <?php echo (isset($_GET['status']) && $_GET['status'] == 'cancelled') ? 'selected' : ''; ?>>Cancelled</option>
            </select>
            
            <input type="date" name="date_from" value="<?php echo isset($_GET['date_from']) ? htmlspecialchars($_GET['date_from']) : ''; ?>" placeholder="From">
            <input type="date" name="date_to" value="<?php echo isset($_GET['date_to']) ? htmlspecialchars($_GET['date_to']) : ''; ?>" placeholder="To">
            
            <button type="submit" class="btn btn-secondary">Filter</button>
            <a href="manage_orders.php" class="btn btn-outline">Clear</a>
        </form>
    </div>

    <div class="orders-table">
        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Items</th>
                    <th>Total</th>
                    <th>Payment</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($orders_result && $orders_result->num_rows > 0): ?>
                    <?php while ($order = $orders_result->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <strong>#<?php echo str_pad($order['id'], 6, '0', STR_PAD_LEFT); ?></strong>
                            </td>
                            <td>
                                <div><?php echo htmlspecialchars($order['full_name']); ?></div>
                                <small><?php echo htmlspecialchars($order['email']); ?></small>
                                <div><small>📞 <?php echo htmlspecialchars($order['phone']); ?></small></div>
                            </td>
                            <td><?php echo $order['item_count']; ?> items</td>
                            <td><strong>₹<?php echo number_format($order['total_price'], 2); ?></strong></td>
                            <td><?php echo ucfirst(str_replace('_', ' ', $order['payment_method'])); ?></td>
                            <td>
                                <span class="status-badge status-<?php echo $order['status']; ?>">
                                    <?php echo ucfirst($order['status']); ?>
                                </span>
                            </td>
                            <td>
                                <div><?php echo date('M d, Y', strtotime($order['created_at'])); ?></div>
                                <small><?php echo date('h:i A', strtotime($order['created_at'])); ?></small>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-outline" onclick="viewOrderDetails(<?php echo $order['id']; ?>)">View</button>
                                <button class="btn btn-sm btn-primary" onclick="updateOrderStatus(<?php echo $order['id']; ?>, '<?php echo $order['status']; ?>')">Update</button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="no-data">No orders found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if ($total_pages > 1): ?>
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?page=<?php echo $page - 1; ?><?php echo isset($_GET['status']) ? '&status=' . urlencode($_GET['status']) : ''; ?><?php echo isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''; ?>" 
                   class="page-link">« Previous</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <?php if ($i == $page): ?>
                    <span class="page-link active"><?php echo $i; ?></span>
                <?php else: ?>
                    <a href="?page=<?php echo $i; ?><?php echo isset($_GET['status']) ? '&status=' . urlencode($_GET['status']) : ''; ?><?php echo isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''; ?>" 
                       class="page-link"><?php echo $i; ?></a>
                <?php endif; ?>
            <?php endfor; ?>

            <?php if ($page < $total_pages): ?>
                <a href="?page=<?php echo $page + 1; ?><?php echo isset($_GET['status']) ? '&status=' . urlencode($_GET['status']) : ''; ?><?php echo isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''; ?>" 
                   class="page-link">Next »</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Order Details Modal -->
<div id="orderDetailsModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Order Details</h3>
            <button class="close-btn" onclick="closeModal('orderDetailsModal')">&times;</button>
        </div>
        <div id="orderDetailsContent">
            <!-- Content will be loaded via AJAX -->
        </div>
    </div>
</div>

<!-- Update Status Modal -->
<div id="updateStatusModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Update Order Status</h3>
            <button class="close-btn" onclick="closeModal('updateStatusModal')">&times;</button>
        </div>
        <form method="POST" action="" class="status-form">
            <input type="hidden" id="status_order_id" name="order_id">
            <div class="form-group">
                <label for="new_status">New Status</label>
                <select id="new_status" name="status" required>
                    <option value="pending">Pending</option>
                    <option value="processing">Processing</option>
                    <option value="shipped">Shipped</option>
                    <option value="delivered">Delivered</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>
            <div class="form-actions">
                <button type="button" class="btn btn-outline" onclick="closeModal('updateStatusModal')">Cancel</button>
                <button type="submit" name="update_status" class="btn btn-primary">Update Status</button>
            </div>
        </form>
    </div>
</div>

<style>
.admin-orders {
    max-width: 1400px;
    margin: 0 auto;
    padding: 20px;
}

.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
}

.header-stats {
    display: flex;
    gap: 15px;
}

.stat-badge {
    padding: 8px 15px;
    border-radius: 20px;
    font-size: 0.9em;
    font-weight: 600;
}

.pending-count {
    background: #fff3cd;
    color: #856404;
}

.processing-count {
    background: #cce5ff;
    color: #004085;
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
    min-width: 150px;
}

.orders-table {
    background: white;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.orders-table table {
    width: 100%;
    border-collapse: collapse;
}

.orders-table th,
.orders-table td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #dee2e6;
}

.orders-table th {
    background: #f8f9fa;
    font-weight: 600;
}

.status-badge {
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 0.8em;
    font-weight: 600;
}

.status-pending {
    background: #fff3cd;
    color: #856404;
}

.status-processing {
    background: #cce5ff;
    color: #004085;
}

.status-shipped {
    background: #d1ecf1;
    color: #0c5460;
}

.status-delivered {
    background: #d4edda;
    color: #155724;
}

.status-cancelled {
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
    max-width: 800px;
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

#orderDetailsContent {
    padding: 20px;
}

.status-form {
    padding: 20px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
}

.form-group select {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 14px;
}

.form-actions {
    display: flex;
    gap: 10px;
    justify-content: flex-end;
}

.order-detail-section {
    margin-bottom: 25px;
}

.order-detail-section h4 {
    margin-bottom: 15px;
    color: #333;
    border-bottom: 1px solid #dee2e6;
    padding-bottom: 8px;
}

.detail-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 15px;
}

.detail-item {
    padding: 10px;
    background: #f8f9fa;
    border-radius: 5px;
}

.detail-label {
    font-weight: 600;
    color: #666;
    font-size: 0.9em;
}

.detail-value {
    margin-top: 5px;
}

.order-items-list {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.order-item-row {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 8px;
}

.order-item-image {
    width: 60px;
    height: 80px;
    object-fit: cover;
    border-radius: 5px;
}

.order-item-details {
    flex: 1;
}

.order-item-name {
    font-weight: 600;
    margin-bottom: 5px;
}

.order-item-meta {
    color: #666;
    font-size: 0.9em;
}

.order-item-price {
    font-weight: 600;
    color: #28a745;
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
    
    .orders-table {
        overflow-x: auto;
    }
    
    .detail-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
function viewOrderDetails(orderId) {
    fetch('get_order_details.php?id=' + orderId)
        .then(response => response.text())
        .then(html => {
            document.getElementById('orderDetailsContent').innerHTML = html;
            document.getElementById('orderDetailsModal').style.display = 'block';
        });
}

function updateOrderStatus(orderId, currentStatus) {
    document.getElementById('status_order_id').value = orderId;
    document.getElementById('new_status').value = currentStatus;
    document.getElementById('updateStatusModal').style.display = 'block';
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
