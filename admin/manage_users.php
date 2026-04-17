<?php
$page_title = "Manage Users";
require_once 'includes/admin-header.php';

// Handle user deletion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_user'])) {
    $user_id = (int)$_POST['user_id'];
    
    // Don't allow deletion of admin users
    $check_admin = "SELECT role FROM users WHERE id = $user_id";
    $admin_result = $conn->query($check_admin);
    
    if ($admin_result && $admin_result->num_rows > 0) {
        $user_role = $admin_result->fetch_assoc()['role'];
        
        if ($user_role != 'admin') {
            $delete_query = "DELETE FROM users WHERE id = $user_id";
            
            if ($conn->query($delete_query)) {
                $_SESSION['message'] = "User deleted successfully!";
                $_SESSION['message_type'] = "success";
            } else {
                $_SESSION['message'] = "Failed to delete user!";
                $_SESSION['message_type'] = "error";
            }
        } else {
            $_SESSION['message'] = "Cannot delete admin users!";
            $_SESSION['message_type'] = "error";
        }
    }
    header('Location: manage_users.php');
    exit();
}

// Get users with filters
$where_clause = "WHERE 1=1";

if (isset($_GET['role']) && !empty($_GET['role'])) {
    $role = clean_input($_GET['role']);
    $where_clause .= " AND role = '$role'";
}

if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = clean_input($_GET['search']);
    $where_clause .= " AND (full_name LIKE '%$search%' OR email LIKE '%$search%' OR phone LIKE '%$search%')";
}

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 15;
$offset = ($page - 1) * $per_page;

$count_query = "SELECT COUNT(*) as total FROM users $where_clause";
$count_result = $conn->query($count_query);
$total_users = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_users / $per_page);

$users_query = "SELECT id, full_name, email, phone, address, role, created_at 
                FROM users $where_clause 
                ORDER BY created_at DESC 
                LIMIT $per_page OFFSET $offset";
$users_result = $conn->query($users_query);
?>

<div class="admin-users">
    <div class="page-header">
        <h1>Manage Users</h1>
        <div class="header-stats">
            <span class="stat-badge total-users">Total Users: <?php 
                $total_query = "SELECT COUNT(*) as count FROM users WHERE role = 'user'";
                echo $conn->query($total_query)->fetch_assoc()['count']; 
            ?></span>
            <span class="stat-badge admin-users">Admins: <?php 
                $admin_query = "SELECT COUNT(*) as count FROM users WHERE role = 'admin'";
                echo $conn->query($admin_query)->fetch_assoc()['count']; 
            ?></span>
        </div>
    </div>

    <div class="filters-section">
        <form method="GET" class="filters-form">
            <input type="text" name="search" placeholder="Search by Name, Email, or Phone..." 
                   value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
            
            <select name="role">
                <option value="">All Roles</option>
                <option value="user" <?php echo (isset($_GET['role']) && $_GET['role'] == 'user') ? 'selected' : ''; ?>>Users</option>
                <option value="admin" <?php echo (isset($_GET['role']) && $_GET['role'] == 'admin') ? 'selected' : ''; ?>>Admins</option>
            </select>
            
            <button type="submit" class="btn btn-secondary">Filter</button>
            <a href="manage_users.php" class="btn btn-outline">Clear</a>
        </form>
    </div>

    <div class="users-table">
        <table>
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Address</th>
                    <th>Role</th>
                    <th>Joined</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($users_result && $users_result->num_rows > 0): ?>
                    <?php while ($user = $users_result->fetch_assoc()): ?>
                        <tr>
                            <td>#<?php echo str_pad($user['id'], 6, '0', STR_PAD_LEFT); ?></td>
                            <td>
                                <strong><?php echo htmlspecialchars($user['full_name']); ?></strong>
                            </td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td><?php echo htmlspecialchars($user['phone']); ?></td>
                            <td>
                                <div style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" 
                                     title="<?php echo htmlspecialchars($user['address']); ?>">
                                    <?php echo htmlspecialchars($user['address']); ?>
                                </div>
                            </td>
                            <td>
                                <span class="role-badge role-<?php echo $user['role']; ?>">
                                    <?php echo ucfirst($user['role']); ?>
                                </span>
                            </td>
                            <td>
                                <div><?php echo date('M d, Y', strtotime($user['created_at'])); ?></div>
                                <small><?php echo date('h:i A', strtotime($user['created_at'])); ?></small>
                            </td>
                            <td>
                                <?php if ($user['role'] != 'admin'): ?>
                                    <button class="btn btn-sm btn-danger" onclick="deleteUser(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['full_name']); ?>')">Delete</button>
                                <?php else: ?>
                                    <span class="text-muted">Admin</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="no-data">No users found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if ($total_pages > 1): ?>
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?page=<?php echo $page - 1; ?><?php echo isset($_GET['role']) ? '&role=' . urlencode($_GET['role']) : ''; ?><?php echo isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''; ?>" 
                   class="page-link">« Previous</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <?php if ($i == $page): ?>
                    <span class="page-link active"><?php echo $i; ?></span>
                <?php else: ?>
                    <a href="?page=<?php echo $i; ?><?php echo isset($_GET['role']) ? '&role=' . urlencode($_GET['role']) : ''; ?><?php echo isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''; ?>" 
                       class="page-link"><?php echo $i; ?></a>
                <?php endif; ?>
            <?php endfor; ?>

            <?php if ($page < $total_pages): ?>
                <a href="?page=<?php echo $page + 1; ?><?php echo isset($_GET['role']) ? '&role=' . urlencode($_GET['role']) : ''; ?><?php echo isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''; ?>" 
                   class="page-link">Next »</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<style>
.admin-users {
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

.total-users {
    background: #d1ecf1;
    color: #0c5460;
}

.admin-users {
    background: #f8d7da;
    color: #721c24;
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
    min-width: 200px;
}

.users-table {
    background: white;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.users-table table {
    width: 100%;
    border-collapse: collapse;
}

.users-table th,
.users-table td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #dee2e6;
}

.users-table th {
    background: #f8f9fa;
    font-weight: 600;
}

.role-badge {
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 0.8em;
    font-weight: 600;
}

.role-user {
    background: #d1ecf1;
    color: #0c5460;
}

.role-admin {
    background: #f8d7da;
    color: #721c24;
}

.text-muted {
    color: #6c757d;
    font-style: italic;
}

.no-data {
    text-align: center;
    color: #666;
    padding: 40px;
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
    
    .users-table {
        overflow-x: auto;
    }
}
</style>

<script>
function deleteUser(userId, userName) {
    if (confirm(`Are you sure you want to delete user "${userName}"? This action cannot be undone.`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = '<input type="hidden" name="user_id" value="' + userId + '"><input type="hidden" name="delete_user" value="1">';
        document.body.appendChild(form);
        form.submit();
    }
}
</script>

<?php require_once 'includes/admin-footer.php'; ?>
