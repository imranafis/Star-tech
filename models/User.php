<?php

class User {
    private $conn;
    private $table = "users";

    public $id;
    public $username;
    public $email;
    public $password;
    public $is_admin;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

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
            
            if (password_verify($password, $row['password'])) {
                unset($row['password']);
                return $row;
            }
        }
        
        return false;
    }

    public function usernameExists($username) {
        $query = "SELECT id FROM " . $this->table . " WHERE username = :username LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":username", $username);
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    }

   public function emailExists($email) {
        $query = "SELECT id FROM " . $this->table . " WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $email);
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    }

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

    
    public function getAllUsers() {
        $query = "SELECT id, username, email, is_admin, created_at 
                  FROM " . $this->table . " 
                  ORDER BY created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

   
    public function countUsers() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        $result = $stmt->fetch();
        return $result['total'];
    }


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