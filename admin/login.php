<?php
$page_title = "Admin Login";
require_once '../includes/auth.php';

// Redirect if already logged in
if (is_logged_in()) {
    if (is_admin()) {
        redirect('dashboard.php');
    } else {
        redirect('../index.php');
    }
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = clean_input($_POST['email']);
    $password = $_POST['password'];

    if (empty($email)) {
        $errors[] = "Email is required";
    }

    if (empty($password)) {
        $errors[] = "Password is required";
    }

    if (empty($errors)) {
        $sql = "SELECT * FROM users WHERE email = '$email'";
        $result = $conn->query($sql);

        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            
            if (password_verify($password, $user['password'])) {
                if ($user['role'] == 'admin') {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = $user['full_name'];
                    $_SESSION['user_email'] = $user['email'];
                    $_SESSION['user_role'] = $user['role'];

                    $_SESSION['message'] = "Welcome back, Admin!";
                    $_SESSION['message_type'] = "success";
                    redirect('dashboard.php');
                } else {
                    $errors[] = "Access denied. This account is not an admin account.";
                }
            } else {
                $errors[] = "Invalid email or password";
            }
        } else {
            $errors[] = "Invalid email or password";
        }
    }
}

require_once '../includes/header.php';
?>

<div class="admin-login-container">
    <div class="admin-login-card">
        <div class="admin-login-header">
            <div class="admin-logo">
                <h1>🔧 Admin Panel</h1>
                <h2>BookNest</h2>
            </div>
            <p>Enter your admin credentials to access the dashboard</p>
        </div>
        
        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <?php foreach ($errors as $error): ?>
                    <p><?php echo $error; ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php display_message(); ?>

        <form method="POST" action="" class="admin-login-form">
            <div class="form-group">
                <label for="email">Admin Email</label>
                <input type="email" id="email" name="email" required 
                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : 'admin@booknest.com'; ?>"
                       placeholder="admin@booknest.com">
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <div class="password-input">
                    <input type="password" id="password" name="password" required 
                           placeholder="Enter your password">
                    <button type="button" class="toggle-password" onclick="togglePassword('password')">
                        <i class="eye-icon">👁️</i>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn btn-admin-login">🔐 Login to Admin Panel</button>
        </form>

        <div class="admin-login-footer">
            <div class="admin-info">
                <h4>Default Admin Credentials:</h4>
                <p><strong>Email:</strong> admin@booknest.com</p>
                <p><strong>Password:</strong> admin123</p>
            </div>
            <div class="login-links">
                <a href="../index.php">← Back to Website</a>
                <a href="../auth/login.php">Customer Login</a>
            </div>
        </div>
    </div>
</div>

<style>
.admin-login-container {
    min-height: 100vh;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
}

.admin-login-card {
    background: white;
    border-radius: 20px;
    box-shadow: 0 20px 40px rgba(0,0,0,0.1);
    overflow: hidden;
    width: 100%;
    max-width: 450px;
}

.admin-login-header {
    background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
    color: white;
    padding: 40px 30px;
    text-align: center;
}

.admin-logo h1 {
    margin: 0 0 5px 0;
    font-size: 2em;
}

.admin-logo h2 {
    margin: 0;
    font-size: 1.2em;
    font-weight: 300;
    opacity: 0.9;
}

.admin-login-header p {
    margin: 15px 0 0 0;
    opacity: 0.8;
    font-size: 0.95em;
}

.admin-login-form {
    padding: 40px 30px 20px;
}

.form-group {
    margin-bottom: 25px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #333;
}

.form-group input {
    width: 100%;
    padding: 15px;
    border: 2px solid #e1e8ed;
    border-radius: 10px;
    font-size: 16px;
    transition: all 0.3s ease;
}

.form-group input:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.password-input {
    position: relative;
}

.toggle-password {
    position: absolute;
    right: 15px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    cursor: pointer;
    font-size: 18px;
    color: #666;
    transition: color 0.3s;
}

.toggle-password:hover {
    color: #667eea;
}

.btn-admin-login {
    width: 100%;
    padding: 15px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    border-radius: 10px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    margin-bottom: 20px;
}

.btn-admin-login:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
}

.admin-login-footer {
    padding: 20px 30px 30px;
    border-top: 1px solid #e1e8ed;
}

.admin-info {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 20px;
}

.admin-info h4 {
    margin: 0 0 10px 0;
    color: #333;
    font-size: 14px;
}

.admin-info p {
    margin: 5px 0;
    color: #666;
    font-size: 13px;
}

.login-links {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.login-links a {
    color: #667eea;
    text-decoration: none;
    font-size: 14px;
    transition: color 0.3s;
}

.login-links a:hover {
    color: #764ba2;
    text-decoration: underline;
}

@media (max-width: 480px) {
    .admin-login-container {
        padding: 10px;
    }
    
    .admin-login-header {
        padding: 30px 20px;
    }
    
    .admin-login-form {
        padding: 30px 20px 20px;
    }
    
    .admin-login-footer {
        padding: 20px;
    }
    
    .login-links {
        flex-direction: column;
        gap: 10px;
        text-align: center;
    }
}
</style>

<script>
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const icon = field.nextElementSibling.querySelector('.eye-icon');
    
    if (field.type === 'password') {
        field.type = 'text';
        icon.textContent = '🙈';
    } else {
        field.type = 'password';
        icon.textContent = '👁️';
    }
}
</script>

<?php require_once '../includes/footer.php'; ?>
