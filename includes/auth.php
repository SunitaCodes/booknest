<?php
// Smart path detection - works from any directory
$current_dir = dirname($_SERVER['PHP_SELF']);

if (strpos($current_dir, '/admin') !== false || strpos($current_dir, '/user') !== false || strpos($current_dir, '/auth') !== false) {
    // We're in a subdirectory
    require_once '../config/db.php';
} else {
    // We're in the root directory
    require_once 'config/db.php';
}

// Check if user is logged in, redirect if not
function require_login() {
    if (!is_logged_in()) {
        $_SESSION['message'] = "Please login to access this page.";
        $_SESSION['message_type'] = "error";
        
        // Determine the correct path based on current directory
        $current_dir = basename(dirname($_SERVER['PHP_SELF']));
        if ($current_dir === 'admin') {
            header('Location: login.php');
        } elseif ($current_dir === 'auth') {
            header('Location: login.php');
        } elseif ($current_dir === 'user') {
            header('Location: ../auth/login.php');
        } else {
            header('Location: auth/login.php');
        }
        exit();
    }
}

// Check if user is admin (for admin pages)
function require_admin() {
    if (!is_admin()) {
        $_SESSION['message'] = "Access denied. Admin privileges required.";
        $_SESSION['message_type'] = "error";
        
        // Determine the correct path based on current directory
        $current_dir = basename(dirname($_SERVER['PHP_SELF']));
        if ($current_dir === 'admin') {
            header('Location: ../index.php');
        } else {
            header('Location: ../index.php');
        }
        exit();
    }
}

// Check if user is regular user (for user pages)
function require_user() {
    if (is_admin()) {
        $_SESSION['message'] = "This page is for regular users only.";
        $_SESSION['message_type'] = "error";
        
        // Determine the correct path based on current directory
        $current_dir = basename(dirname($_SERVER['PHP_SELF']));
        if ($current_dir === 'user') {
            header('Location: ../admin/dashboard.php');
        } else {
            header('Location: admin/dashboard.php');
        }
        exit();
    }
}
?>
