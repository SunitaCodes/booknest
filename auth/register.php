<?php
require_once '../config/db.php';

if (is_logged_in()) {
    redirect('../index.php');
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = clean_input($_POST['full_name']);
    $email = clean_input($_POST['email']);
    $phone = clean_input($_POST['phone']);
    $address = clean_input($_POST['address']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validation
    if (empty($full_name)) {
        $errors[] = "Full name is required";
    } elseif (!preg_match("/^[a-zA-Z\s]+$/", $full_name)) {
        $errors[] = "Full name should contain only alphabets";
    }

    if (empty($email)) {
        $errors[] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }

    if (empty($phone)) {
        $errors[] = "Phone number is required";
    } elseif (!preg_match("/^[0-9]{10}$/", $phone)) {
        $errors[] = "Phone number must be exactly 10 digits";
    }

    if (empty($address)) {
        $errors[] = "Address is required";
    } elseif (!preg_match("/^[a-zA-Z0-9\s,\-#]+$/", $address)) {
        $errors[] = "Address contains invalid characters";
    }

    if (empty($password)) {
        $errors[] = "Password is required";
    } elseif (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters";
    } elseif (!preg_match("/^(?=.*[A-Za-z])(?=.*\d).+$/", $password)) {
        $errors[] = "Password must contain both letters and numbers";
    }

    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match";
    }

    // Check if email already exists
    if (empty($errors)) {
        $check_email = "SELECT id FROM users WHERE email = '$email'";
        $result = $conn->query($check_email);
        
        if ($result->num_rows > 0) {
            $errors[] = "Email already exists";
        }
    }

    // Register user if no errors
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (full_name, email, phone, address, password) VALUES 
                ('$full_name', '$email', '$phone', '$address', '$hashed_password')";
        
        if ($conn->query($sql)) {
            $_SESSION['message'] = "Registration successful! Please login.";
            $_SESSION['message_type'] = "success";
            redirect('login.php');
        } else {
            $errors[] = "Registration failed. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - BookNest</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-form">
            <h2>Create Account</h2>
            <p>Join BookNest today!</p>
            
            <?php if (!empty($errors)): ?>
                <div class="alert alert-error">
                    <?php foreach ($errors as $error): ?>
                        <p><?php echo $error; ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label for="full_name">Full Name</label>
                    <input type="text" id="full_name" name="full_name" required 
                           value="<?php echo isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : ''; ?>"
                           pattern="[A-Za-z\s]+" title="Only alphabets allowed">
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required 
                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                </div>

                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" id="phone" name="phone" required maxlength="10"
                           value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>"
                           pattern="[0-9]{10}" title="Exactly 10 digits required">
                </div>

                <div class="form-group">
                    <label for="address">Address</label>
                    <textarea id="address" name="address" required rows="3"
                              pattern="[A-Za-z0-9\s,\-#]+" title="Only alphabets, numbers, and basic punctuation allowed"><?php echo isset($_POST['address']) ? htmlspecialchars($_POST['address']) : ''; ?></textarea>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="password-input">
                        <input type="password" id="password" name="password" required 
                               minlength="8" title="Minimum 8 characters with letters and numbers">
                        <button type="button" class="toggle-password" onclick="togglePassword('password')">
                            <i class="eye-icon">👁️</i>
                        </button>
                    </div>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <div class="password-input">
                        <input type="password" id="confirm_password" name="confirm_password" required>
                        <button type="button" class="toggle-password" onclick="togglePassword('confirm_password')">
                            <i class="eye-icon">👁️</i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Register</button>
            </form>

            <div class="auth-links">
                <p>Already have an account? <a href="login.php">Login here</a></p>
                <p><a href="../index.php">Back to Home</a></p>
            </div>
        </div>
    </div>

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
</body>
</html>
