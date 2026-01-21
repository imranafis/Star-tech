
<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Product.php';

class ProductController {
    private $db;
    private $product;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->product = new Product($this->db);
    }

    public function getAllProducts() {
        return $this->product->getAll();
    }

    public function getProductsByCategory($category) {
        return $this->product->getByCategory($category);
    }

    public function getProductById($id) {
        return $this->product->getById($id);
    }

    public function getCategories() {
        return $this->product->getCategories();
    }

    public function addProduct($name, $category, $price, $image) {
        return $this->product->create($name, $category, $price, $image);
    }

    // ADD THIS METHOD FOR UPDATING PRODUCTS
    public function updateProduct($id, $name, $category, $price, $image) {
        return $this->product->update($id, $name, $category, $price, $image);
    }

    // ADD THIS METHOD FOR DELETING PRODUCTS
    public function deleteProduct($id) {
        return $this->product->delete($id);
    }
}
?>
