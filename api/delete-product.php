<?php
// Start output buffering
ob_start();

// Disable error display
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Set JSON header
header('Content-Type: application/json');

try {
    // Check if file exists before requiring
    $controllerPath = __DIR__ . '/../controllers/ProductController.php';
    if (!file_exists($controllerPath)) {
        throw new Exception('ProductController not found');
    }
    
    require_once $controllerPath;

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    $data = json_decode(file_get_contents('php://input'), true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Invalid JSON data');
    }
    
    $productId = isset($data['id']) ? intval($data['id']) : 0;

    if ($productId <= 0) {
        throw new Exception('Invalid product ID');
    }

    $productController = new ProductController();
    $product = $productController->getProductById($productId);

    if (!$product) {
        throw new Exception('Product not found');
    }

    $result = $productController->deleteProduct($productId);

    if (!$result) {
        throw new Exception('Failed to delete product from database');
    }
    
    // Delete image file
    $imagePath = __DIR__ . '/../assets/images/products/' . $product['image'];
    if (file_exists($imagePath)) {
        @unlink($imagePath);
    }
    
    // Clear any buffered output
    ob_end_clean();
    
    echo json_encode([
        'success' => true, 
        'message' => 'Product deleted successfully'
    ]);
    
} catch (Exception $e) {
    // Clear any buffered output
    ob_end_clean();
    
    echo json_encode([
        'success' => false, 
        'message' => $e->getMessage()
    ]);
}

exit;
?>