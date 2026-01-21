<?php
class Order {
    private $conn;
    private $table = "orders";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create($userId, $customerName, $address, $totalAmount, $orderType, $items) {
        try {
            $this->conn->beginTransaction();
            
            $query = "INSERT INTO " . $this->table . " (user_id, customer_name, address, total_amount, order_type) 
                      VALUES (:user_id, :customer_name, :address, :total_amount, :order_type)";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":user_id", $userId);
            $stmt->bindParam(":customer_name", $customerName);
            $stmt->bindParam(":address", $address);
            $stmt->bindParam(":total_amount", $totalAmount);
            $stmt->bindParam(":order_type", $orderType);
            $stmt->execute();
            
            $orderId = $this->conn->lastInsertId();
            
            $itemQuery = "INSERT INTO order_items (order_id, product_id, quantity, price) 
                          VALUES (:order_id, :product_id, :quantity, :price)";
            $itemStmt = $this->conn->prepare($itemQuery);
            
            foreach ($items as $item) {
                $itemStmt->bindParam(":order_id", $orderId);
                $itemStmt->bindParam(":product_id", $item['product_id']);
                $itemStmt->bindParam(":quantity", $item['quantity']);
                $itemStmt->bindParam(":price", $item['price']);
                $itemStmt->execute();
            }
            
            $this->conn->commit();
            return $orderId;
            
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }
}
?>