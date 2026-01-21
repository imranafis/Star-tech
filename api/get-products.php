<?php
require_once '../controllers/ProductController.php';

header('Content-Type: application/json');

$productController = new ProductController();
$products = $productController->getAllProducts();

echo json_encode([
    'success' => true,
    'products' => $products
]);
?>