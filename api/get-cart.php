<?php
// api/get-cart.php
require_once '../config/config.php';
require_once '../controllers/CartController.php';

header('Content-Type: application/json');
session_start();

$cartController = new CartController();
$cart = $cartController->getCartSummary();

echo json_encode([
    'success' => true,
    'cart' => $cart
]);
?>