<?php
/**
 * CartController
 * Handles shopping cart operations
 * Manages session-based cart functionality
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Product.php';

class CartController {
    private $db;
    private $product;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->product = new Product($this->db);
        $this->initCart();
    }
    
    /**
     * Initialize cart session if not exists
     */
    private function initCart() {
        if (!isset($_SESSION['cart']) || !isset($_SESSION['cart']['items'])) {
            $_SESSION['cart'] = [
                'items' => [],
                'total' => 0.00,
                'count' => 0
            ];
        }
    }

    /**
     * Add single product to cart
     * @param int $productId Product ID
     * @return array Success status and message
     */
    public function addToCart($productId) {
        if (!isLoggedIn()) {
            return ['success' => false, 'message' => 'Please login to add items to cart'];
        }
        
        $product = $this->product->getById($productId);
        
        if (!$product) {
            return ['success' => false, 'message' => 'Product not found'];
        }
        
        // Check if product already in cart
        $found = false;
        foreach ($_SESSION['cart']['items'] as &$item) {
            if (isset($item['product_id']) && $item['product_id'] == $productId) {
                $item['quantity'] += 1;
                $found = true;
                break;
            }
        }
        
        // Add new product to cart
        if (!$found) {
            $_SESSION['cart']['items'][] = [
                'product_id' => $product['id'],
                'name' => $product['name'],
                'price' => floatval($product['price']),
                'image' => $product['image'],
                'quantity' => 1
            ];
        }
        
        // Update cart totals
        $this->updateCartTotals();
        
        return ['success' => true, 'message' => 'Product added to cart', 'cart_count' => $_SESSION['cart']['count']];
    }

    /**
     * Add PC build to cart
     * @param array $components Array of selected components
     * @return array Success status and message
     */
    public function addBuildToCart($components) {
        if (!isLoggedIn()) {
            return ['success' => false, 'message' => 'Please login to build PC'];
        }
        
        if (empty($components) || count($components) < 3) {
            return ['success' => false, 'message' => 'Please select at least 3 components'];
        }
        
        // Clear existing items and add build as a special item
        $_SESSION['cart']['items'] = [
            [
                'type' => 'build_pc',
                'name' => 'Custom PC Build',
                'components' => $components,
                'quantity' => 1
            ]
        ];
        
        // Calculate total for build
        $total = 0;
        foreach ($components as $component) {
            if (isset($component['price'])) {
                $total += floatval($component['price']);
            }
        }
        
        $_SESSION['cart']['items'][0]['price'] = $total;
        $this->updateCartTotals();
        
        return ['success' => true, 'message' => 'PC build added to cart'];
    }

    /**
     * Update cart totals
     */
    private function updateCartTotals() {
        $total = 0.00;
        $count = 0;
        
        foreach ($_SESSION['cart']['items'] as $item) {
            if (isset($item['price']) && isset($item['quantity'])) {
                $total += floatval($item['price']) * intval($item['quantity']);
                $count += intval($item['quantity']);
            }
        }
        
        $_SESSION['cart']['total'] = $total;
        $_SESSION['cart']['count'] = $count;
    }

    /**
     * Get cart contents
     * @return array Cart data
     */
    public function getCart() {
        return $_SESSION['cart'];
    }
    
    /**
     * Get cart summary for display
     * @return array Cart summary data
     */
    public function getCartSummary() {
        return [
            'items' => $_SESSION['cart']['items'],
            'total' => $_SESSION['cart']['total'],
            'count' => $_SESSION['cart']['count']
        ];
    }

    /**
     * Get cart total
     * @return float Total cart amount
     */
    public function getCartTotal() {
        return $_SESSION['cart']['total'];
    }
    
    /**
     * Get cart item count
     * @return int Number of items in cart
     */
    public function getCartCount() {
        return $_SESSION['cart']['count'];
    }
    
    /**
     * Clear cart
     * @return bool Success status
     */
    public function clearCart() {
        $_SESSION['cart'] = [
            'items' => [],
            'total' => 0.00,
            'count' => 0
        ];
        return true;
    }
    
    /**
     * Update quantity of product in cart
     * @param int $productId Product ID
     * @param int $quantity New quantity
     * @return array Success status and message
     */
    public function updateQuantity($productId, $quantity) {
        if (!isLoggedIn()) {
            return ['success' => false, 'message' => 'Please login first'];
        }
        
        if ($quantity < 1) {
            return ['success' => false, 'message' => 'Invalid quantity'];
        }
        
        foreach ($_SESSION['cart']['items'] as &$item) {
            if (isset($item['product_id']) && $item['product_id'] == $productId) {
                $item['quantity'] = $quantity;
                $this->updateCartTotals();
                return ['success' => true, 'message' => 'Quantity updated'];
            }
        }
        
        return ['success' => false, 'message' => 'Product not found in cart'];
    }
    
    /**
     * Remove item from cart
     * @param int $itemIndex Index of item to remove
     * @return array Success status and message
     */
    public function removeFromCart($itemIndex) {
        if (!isLoggedIn()) {
            return ['success' => false, 'message' => 'Please login first'];
        }
        
        if (isset($_SESSION['cart']['items'][$itemIndex])) {
            array_splice($_SESSION['cart']['items'], $itemIndex, 1);
            $this->updateCartTotals();
            return ['success' => true, 'message' => 'Item removed from cart'];
        }
        
        return ['success' => false, 'message' => 'Item not found'];
    }
    
    /**
     * Validate cart before checkout
     * @return array Validation result
     */
    public function validateCart() {
        if (!isLoggedIn()) {
            return ['valid' => false, 'message' => 'Please login to checkout'];
        }
        
        if (empty($_SESSION['cart']['items'])) {
            return ['valid' => false, 'message' => 'Cart is empty'];
        }
        
        foreach ($_SESSION['cart']['items'] as $item) {
            if (isset($item['product_id'])) {
                $product = $this->product->getById($item['product_id']);
                if (!$product) {
                    $this->clearCart();
                    return ['valid' => false, 'message' => 'Product no longer available'];
                }
            } else if (isset($item['type']) && $item['type'] === 'build_pc') {
                foreach ($item['components'] as $component) {
                    $product = $this->product->getById($component['id']);
                    if (!$product) {
                        $this->clearCart();
                        return ['valid' => false, 'message' => 'One or more components no longer available'];
                    }
                }
            }
        }
        
        return ['valid' => true, 'message' => 'Cart is valid'];
    }
}
?>