<?php
require_once '../includes/header.php';
require_once '../includes/auth.php';
require_user();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['order_id'])) {
    $order_id = (int)$_POST['order_id'];
    $user_id = $_SESSION['user_id'];
    
    // Check if order belongs to user and is in pending status
    $check_query = "SELECT id FROM orders WHERE id = $order_id AND user_id = $user_id AND status = 'pending'";
    $result = $conn->query($check_query);
    
    if ($result && $result->num_rows > 0) {
        // Update order status to cancelled
        $update_query = "UPDATE orders SET status = 'cancelled' WHERE id = $order_id";
        if ($conn->query($update_query)) {
            // Restore stock to products
            $restore_query = "UPDATE products p 
                             SET p.stock = p.stock + (
                                 SELECT COALESCE(SUM(oi.quantity), 0) 
                                 FROM order_items oi 
                                 WHERE oi.order_id = $order_id AND oi.product_id = p.id
                             )
                             WHERE p.id IN (
                                 SELECT product_id FROM order_items WHERE order_id = $order_id
                             )";
            $conn->query($restore_query);
            
            $_SESSION['message'] = "Order cancelled successfully. Stock has been restored.";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Failed to cancel order. Please try again.";
            $_SESSION['message_type'] = "error";
        }
    } else {
        $_SESSION['message'] = "Order not found or cannot be cancelled.";
        $_SESSION['message_type'] = "error";
    }
} else {
    $_SESSION['message'] = "Invalid request.";
    $_SESSION['message_type'] = "error";
}

header('Location: orders.php');
exit();
?>
