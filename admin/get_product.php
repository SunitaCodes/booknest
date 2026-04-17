<?php
require_once '../config/db.php';
require_once '../includes/auth.php';
require_admin();

if (!isset($_GET['id'])) {
    exit();
}

$id = (int)$_GET['id'];
$query = "SELECT * FROM products WHERE id = $id";
$result = $conn->query($query);

if ($result && $result->num_rows > 0) {
    $product = $result->fetch_assoc();
    header('Content-Type: application/json');
    echo json_encode($product);
} else {
    header('HTTP/1.0 404 Not Found');
    echo json_encode(['error' => 'Product not found']);
}
?>
