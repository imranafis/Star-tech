<?php
/**
 * User Model
 * Handles all user-related database operations
 * Including authentication, registration, and profile management
 */

class User {
    private $conn;
    private $table = "users";

    // User properties
    public $id;
    public $username;
    public $email;
    public $password;
    public $is_admin;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Register new user
     * @param string $username Username
     * @param string $email Email address
     * @param string $password Plain text password (will be hashed)
     * @return bool Registration success status
     */
    public function register($username, $email, $password) {
        $query = "INSERT INTO " . $this->table . " 
                  (username, email, password) 
                  VALUES (:username, :email, :password)";
        
        $stmt = $this->conn->prepare($query);
        
        // Hash password using bcrypt
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        
        // Bind parameters
        $stmt->bindParam(":username", $username);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":password", $hashedPassword);
        
        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("User registration error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * User login
     * @param string $username Username
     * @param string $password Plain text password
     * @return array|false User data if successful, false otherwise
     */
    public function login($username, $password) {
        $query = "SELECT id, username, email, password, is_admin, created_at 
                  FROM " . $this->table . " 
                  WHERE username = :username 
                  LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":username", $username);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch();
            
            // Verify password using password_verify
            if (password_verify($password, $row['password'])) {
                // Don't return password in the result
                unset($row['password']);
                return $row;
            }
        }
        
        return false;
    }

    /**
     * Check if username exists
     * @param string $username Username to check
     * @return bool True if exists, false otherwise
     */
    public function usernameExists($username) {
        $query = "SELECT id FROM " . $this->table . " WHERE username = :username LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":username", $username);
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    }

    /**
     * Check if email exists
     * @param string $email Email to check
     * @return bool True if exists, false otherwise
     */
    public function emailExists($email) {
        $query = "SELECT id FROM " . $this->table . " WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $email);
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    }

    /**
     * Get user by ID
     * @param int $id User ID
     * @return array|false User data or false if not found
     */
    public function getById($id) {
        $query = "SELECT id, username, email, password, is_admin, created_at 
                  FROM " . $this->table . " 
                  WHERE id = :id 
                  LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            return $stmt->fetch();
        }
        
        return false;
    }

    /**
     * Get user by username
     * @param string $username Username
     * @return array|false User data or false if not found
     */
    public function getByUsername($username) {
        $query = "SELECT id, username, email, is_admin, created_at 
                  FROM " . $this->table . " 
                  WHERE username = :username 
                  LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":username", $username);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            return $stmt->fetch();
        }
        
        return false;
    }

    /**
     * Get user by email
     * @param string $email Email address
     * @return array|false User data or false if not found
     */
    public function getByEmail($email) {
        $query = "SELECT id, username, email, is_admin, created_at 
                  FROM " . $this->table . " 
                  WHERE email = :email 
                  LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $email);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            return $stmt->fetch();
        }
        
        return false;
    }

    /**
     * Update user password
     * @param int $userId User ID
     * @param string $newPassword New password (will be hashed)
     * @return bool Update success status
     */
    public function updatePassword($userId, $newPassword) {
        $query = "UPDATE " . $this->table . " 
                  SET password = :password 
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
        
        $stmt->bindParam(":password", $hashedPassword);
        $stmt->bindParam(":id", $userId);
        
        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Password update error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update user email
     * @param int $userId User ID
     * @param string $email New email address
     * @return bool Update success status
     */
    public function updateEmail($userId, $email) {
        $query = "UPDATE " . $this->table . " 
                  SET email = :email 
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":id", $userId);
        
        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Email update error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update user profile
     * @param int $userId User ID
     * @param string $username New username
     * @param string $email New email
     * @return bool Update success status
     */
    public function updateProfile($userId, $username, $email) {
        $query = "UPDATE " . $this->table . " 
                  SET username = :username, email = :email 
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":username", $username);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":id", $userId);
        
        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Profile update error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete user
     * @param int $userId User ID
     * @return bool Delete success status
     */
    public function delete($userId) {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $userId);
        
        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("User deletion error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get all users (admin function)
     * @return array Array of all users
     */
    public function getAllUsers() {
        $query = "SELECT id, username, email, is_admin, created_at 
                  FROM " . $this->table . " 
                  ORDER BY created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    /**
     * Count total users
     * @return int Total user count
     */
    public function countUsers() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        $result = $stmt->fetch();
        return $result['total'];
    }

    /**
     * Make user admin
     * @param int $userId User ID
     * @return bool Update success status
     */
    public function makeAdmin($userId) {
        $query = "UPDATE " . $this->table . " SET is_admin = 1 WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $userId);
        
        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Make admin error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Remove admin rights
     * @param int $userId User ID
     * @return bool Update success status
     */
    public function removeAdmin($userId) {
        $query = "UPDATE " . $this->table . " SET is_admin = 0 WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $userId);
        
        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Remove admin error: " . $e->getMessage());
            return false;
        }
    }
}
?>