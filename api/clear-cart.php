<?php
// api/clear-cart.php
require_once '../config/config.php';
require_once '../controllers/CartController.php';

header('Content-Type: application/json');
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cartController = new CartController();
    
    if ($cartController->clearCart()) {
        echo json_encode(['success' => true, 'message' => 'Cart cleared']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to clear cart']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>