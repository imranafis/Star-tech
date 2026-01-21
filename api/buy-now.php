<?php
// api/buy-now.php
session_start();
header('Content-Type: application/json');

if (!isset($_POST['product_id'])) {
    echo json_encode(['success' => false, 'message' => 'Product ID is required']);
    exit();
}

$productId = intval($_POST['product_id']);
$productName = isset($_POST['product_name']) ? $_POST['product_name'] : 'Product';
$price = isset($_POST['price']) ? floatval($_POST['price']) : 0.00;

// Store product in session (clear any existing build)
unset($_SESSION['build_order']);

$_SESSION['product_order'] = [
    'type' => 'single_product',
    'product_id' => $productId,
    'name' => $productName,
    'price' => $price,
    'total' => $price,
    'timestamp' => time()
];

echo json_encode([
    'success' => true,
    'message' => 'Product saved for checkout',
    'total' => $price,
    'redirect' => 'index.php?page=checkout'
]);
?>