<?php
// api/checkout.php
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../models/Order.php';
require_once '../controllers/CartController.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Please login first']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

try {
    $cartController = new CartController();
    $cart = $cartController->getCart();
    
    // Check if cart is empty
    if (empty($cart['items'])) {
        echo json_encode(['success' => false, 'message' => 'Cart is empty']);
        exit();
    }
    
    // Get order data
    $orderData = json_decode($_POST['order_data'], true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode(['success' => false, 'message' => 'Invalid order data']);
        exit();
    }
    
    // Validate required fields
    $required = ['customerName', 'email', 'phone', 'address'];
    foreach ($required as $field) {
        if (empty($orderData[$field])) {
            echo json_encode(['success' => false, 'message' => "Missing required field: $field"]);
            exit();
        }
    }
    
    // Create order in database
    $database = new Database();
    $db = $database->getConnection();
    $orderModel = new Order($db);
    
    $userId = $_SESSION['user_id'];
    $customerName = sanitize($orderData['customerName']);
    $email = sanitize($orderData['email']);
    $phone = sanitize($orderData['phone']);
    $address = sanitize($orderData['address']);
    $paymentMethod = isset($orderData['paymentMethod']) ? sanitize($orderData['paymentMethod']) : 'cod';
    $totalAmount = $cart['total'];
    
    // Generate order items from cart
    $orderItems = [];
    foreach ($cart['items'] as $item) {
        if (isset($item['product_id'])) {
            $orderItems[] = [
                'product_id' => $item['product_id'],
                'name' => $item['name'],
                'quantity' => $item['quantity'],
                'price' => $item['price']
            ];
        } elseif (isset($item['type']) && $item['type'] === 'build_pc') {
            foreach ($item['components'] as $component) {
                $orderItems[] = [
                    'product_id' => $component['id'],
                    'name' => $component['name'],
                    'quantity' => 1,
                    'price' => $component['price']
                ];
            }
        }
    }
    
    // Save order
    $orderId = $orderModel->createOrder($userId, $customerName, $email, $phone, $address, $paymentMethod, $totalAmount, $orderItems);
    
    if ($orderId) {
        // Clear cart after successful order
        $cartController->clearCart();
        
        echo json_encode([
            'success' => true,
            'order_id' => $orderId,
            'message' => 'Order placed successfully'
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to create order']);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Server error: ' . $e->getMessage()
    ]);
}
?>