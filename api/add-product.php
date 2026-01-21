<?php
require_once '../config/config.php';
require_once '../controllers/ProductController.php';

header('Content-Type: application/json');

if (!isAdmin()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productName = isset($_POST['productName']) ? sanitize($_POST['productName']) : '';
    $category = isset($_POST['category']) ? sanitize($_POST['category']) : '';
    $price = isset($_POST['price']) ? floatval($_POST['price']) : 0;
    
    if (empty($productName) || empty($category) || $price <= 0) {
        echo json_encode(['success' => false, 'message' => 'All fields are required']);
        exit;
    }
    
    if (strlen($productName) < 3) {
        echo json_encode(['success' => false, 'message' => 'Product name must be at least 3 characters']);
        exit;
    }
    
    if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['success' => false, 'message' => 'Image upload failed']);
        exit;
    }
    
    $file = $_FILES['image'];
    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
    
    if (!in_array($file['type'], $allowedTypes)) {
        echo json_encode(['success' => false, 'message' => 'Invalid image type']);
        exit;
    }
    
    if ($file['size'] > 5 * 1024 * 1024) {
        echo json_encode(['success' => false, 'message' => 'Image size must be less than 5MB']);
        exit;
    }
    
    $uploadDir = UPLOAD_DIR;
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '_' . time() . '.' . $extension;
    $destination = $uploadDir . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $destination)) {
        $productController = new ProductController();
        
        if ($productController->addProduct($productName, $category, $price, $filename)) {
            echo json_encode(['success' => true, 'message' => 'Product added successfully']);
        } else {
            unlink($destination);
            echo json_encode(['success' => false, 'message' => 'Failed to add product to database']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to upload image']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>