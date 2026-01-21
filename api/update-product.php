<?php
ob_start();

ini_set('display_errors', 0);
error_reporting(E_ALL);

header('Content-Type: application/json');

try {
    $controllerPath = __DIR__ . '/../controllers/ProductController.php';
    if (!file_exists($controllerPath)) {
        throw new Exception('ProductController not found at: ' . $controllerPath);
    }
    
    require_once $controllerPath;

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    $productId = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $productName = isset($_POST['productName']) ? trim($_POST['productName']) : '';
    $category = isset($_POST['category']) ? trim($_POST['category']) : '';
    $price = isset($_POST['price']) ? floatval($_POST['price']) : 0;

    if ($productId <= 0) {
        throw new Exception('Invalid product ID');
    }

    if (empty($productName) || empty($category) || $price <= 0) {
        throw new Exception('All fields are required');
    }

    $productController = new ProductController();
    $currentProduct = $productController->getProductById($productId);

    if (!$currentProduct) {
        throw new Exception('Product not found');
    }

    $imageName = $currentProduct['image'];

    // Handle image upload if present
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        $fileType = $_FILES['image']['type'];
        $fileSize = $_FILES['image']['size'];
        
        if (!in_array($fileType, $allowedTypes)) {
            throw new Exception('Invalid image type. Only JPG, PNG, and GIF are allowed');
        }
        
        if ($fileSize > 5 * 1024 * 1024) {
            throw new Exception('Image size too large. Maximum 5MB allowed');
        }
        
        $uploadDir = __DIR__ . '/../assets/images/products/';
        if (!file_exists($uploadDir)) {
            if (!mkdir($uploadDir, 0777, true)) {
                throw new Exception('Failed to create upload directory');
            }
        }
        
        $imageExtension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $imageName = uniqid() . '_' . time() . '.' . $imageExtension;
        $uploadPath = $uploadDir . $imageName;
        
        if (!move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
            throw new Exception('Failed to upload image');
        }
        
        // Delete old image
        if ($currentProduct['image'] && file_exists($uploadDir . $currentProduct['image'])) {
            @unlink($uploadDir . $currentProduct['image']);
        }
    }

    $result = $productController->updateProduct($productId, $productName, $category, $price, $imageName);

    if (!$result) {
        throw new Exception('Failed to update product in database');
    }
    
    ob_end_clean();
    
    echo json_encode([
        'success' => true, 
        'message' => 'Product updated successfully'
    ]);
    
} catch (Exception $e) {
    ob_end_clean();
    
    echo json_encode([
        'success' => false, 
        'message' => $e->getMessage()
    ]);
}

exit;
?>