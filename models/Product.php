
<?php
class Product {
    private $conn;
    private $table = "products";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create($name, $category, $price, $image) {
        $query = "INSERT INTO " . $this->table . " (name, category, price, image) VALUES (:name, :category, :price, :image)";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":name", $name);
        $stmt->bindParam(":category", $category);
        $stmt->bindParam(":price", $price);
        $stmt->bindParam(":image", $image);
        
        return $stmt->execute();
    }

    public function getAll() {
        $query = "SELECT * FROM " . $this->table . " ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    public function getByCategory($category) {
        $query = "SELECT * FROM " . $this->table . " WHERE category = :category ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":category", $category);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        
        return $stmt->fetch();
    }

    public function getCategories() {
        $query = "SELECT DISTINCT category FROM " . $this->table . " ORDER BY category";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    // ADD THIS METHOD FOR UPDATING PRODUCTS
    public function update($id, $name, $category, $price, $image) {
        $query = "UPDATE " . $this->table . " SET name = :name, category = :category, price = :price, image = :image WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":id", $id);
        $stmt->bindParam(":name", $name);
        $stmt->bindParam(":category", $category);
        $stmt->bindParam(":price", $price);
        $stmt->bindParam(":image", $image);
        
        return $stmt->execute();
    }

    // ADD THIS METHOD FOR DELETING PRODUCTS
    public function delete($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        
        return $stmt->execute();
    }
}
?>
