<?php
require_once '../config/config.php';
require_once '../controllers/CartController.php';

header('Content-Type: application/json');
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login first']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cartController = new CartController();
    
    if (isset($_POST['build_components'])) {
        $components = json_decode($_POST['build_components'], true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            echo json_encode(['success' => false, 'message' => 'Invalid components data']);
            exit;
        }
        $result = $cartController->addBuildToCart($components);
    } 
    elseif (isset($_POST['product_id'])) {
        $productId = intval($_POST['product_id']);
        $result = $cartController->addToCart($productId);
    }
    // Check if it's from form data
    elseif (isset($_POST['product_id']) && isset($_POST['product_name']) && isset($_POST['price'])) {
        $productId = intval($_POST['product_id']);
        $result = $cartController->addToCart($productId);
    }
    else {
        echo json_encode(['success' => false, 'message' => 'No product specified']);
        exit;
    }
    
    echo json_encode($result);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>