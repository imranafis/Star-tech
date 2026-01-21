<?php
/**
 * Database Model Class
 * Handles database connection using PDO
 * Provides singleton pattern for connection reuse
 */

class Database {
    // Database credentials
    private $host = "localhost";
    private $db_name = "star_tech";
    private $username = "root";
    private $password = "";
    private $charset = "utf8mb4";
    
    // PDO connection instance
    public $conn;
    
    // Singleton instance
    private static $instance = null;

    /**
     * Get database connection
     * @return PDO connection object
     */
    public function getConnection() {
        $this->conn = null;
        
        try {
            // Create DSN (Data Source Name)
            $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=" . $this->charset;
            
            // PDO options for better security and performance
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::ATTR_PERSISTENT         => false
            ];
            
            // Create PDO connection
            $this->conn = new PDO($dsn, $this->username, $this->password, $options);
            
        } catch(PDOException $e) {
            // Log error (in production, don't display to user)
            error_log("Connection Error: " . $e->getMessage());
            
            // Display user-friendly message
            echo "Database connection error. Please try again later.";
            exit;
        }
        
        return $this->conn;
    }
    
    /**
     * Get singleton instance
     * Ensures only one database connection exists
     * @return Database instance
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Close database connection
     */
    public function closeConnection() {
        $this->conn = null;
    }
    
    /**
     * Begin transaction
     */
    public function beginTransaction() {
        return $this->conn->beginTransaction();
    }
    
    /**
     * Commit transaction
     */
    public function commit() {
        return $this->conn->commit();
    }
    
    /**
     * Rollback transaction
     */
    public function rollback() {
        return $this->conn->rollBack();
    }
    
    /**
     * Get last insert ID
     * @return int last inserted ID
     */
    public function lastInsertId() {
        return $this->conn->lastInsertId();
    }
    
    /**
     * Execute a query
     * @param string $query SQL query
     * @param array $params Parameters to bind
     * @return PDOStatement
     */
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
    
    /**
     * Fetch single row
     * @param string $query SQL query
     * @param array $params Parameters to bind
     * @return array|false
     */
    public function fetchOne($query, $params = []) {
        $stmt = $this->query($query, $params);
        return $stmt->fetch();
    }
    
    /**
     * Fetch all rows
     * @param string $query SQL query
     * @param array $params Parameters to bind
     * @return array
     */
    public function fetchAll($query, $params = []) {
        $stmt = $this->query($query, $params);
        return $stmt->fetchAll();
    }
    
    /**
     * Execute insert/update/delete
     * @param string $query SQL query
     * @param array $params Parameters to bind
     * @return bool
     */
    public function execute($query, $params = []) {
        try {
            $stmt = $this->query($query, $params);
            return true;
        } catch(PDOException $e) {
            return false;
        }
    }
    
    /**
     * Count rows
     * @param string $table Table name
     * @param string $where WHERE clause
     * @param array $params Parameters to bind
     * @return int
     */
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