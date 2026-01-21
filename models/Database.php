<?php


class Database {
    private $host = "localhost";
    private $db_name = "star_tech";
    private $username = "root";
    private $password = "";
    private $charset = "utf8mb4";
    
    public $conn;
    
    private static $instance = null;

    public function getConnection() {
        $this->conn = null;
        
        try {
            $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=" . $this->charset;
            
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::ATTR_PERSISTENT         => false
            ];
            
            $this->conn = new PDO($dsn, $this->username, $this->password, $options);
            
        } catch(PDOException $e) {
            error_log("Connection Error: " . $e->getMessage());
            
            echo "Database connection error. Please try again later.";
            exit;
        }
        
        return $this->conn;
    }
    
 
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function closeConnection() {
        $this->conn = null;
    }
 
    public function beginTransaction() {
        return $this->conn->beginTransaction();
    }
    

    public function commit() {
        return $this->conn->commit();
    }
    
 
    public function rollback() {
        return $this->conn->rollBack();
    }
    
  
    public function lastInsertId() {
        return $this->conn->lastInsertId();
    }
    
    public function query($query, $params = []) {
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute($params);
            return $stmt;
        } catch(PDOException $e) {
            error_log("Query Error: " . $e->getMessage());
            throw $e;
        }
    }
    
    public function fetchOne($query, $params = []) {
        $stmt = $this->query($query, $params);
        return $stmt->fetch();
    }
    
    public function fetchAll($query, $params = []) {
        $stmt = $this->query($query, $params);
        return $stmt->fetchAll();
    }
    
    public function execute($query, $params = []) {
        try {
            $stmt = $this->query($query, $params);
            return true;
        } catch(PDOException $e) {
            return false;
        }
    }
    
    public function count($table, $where = "", $params = []) {
        $query = "SELECT COUNT(*) as count FROM " . $table;
        if ($where) {
            $query .= " WHERE " . $where;
        }
        $result = $this->fetchOne($query, $params);
        return $result ? $result['count'] : 0;
    }
}
?>