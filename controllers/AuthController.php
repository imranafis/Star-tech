<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/User.php';

class AuthController {
    private $db;
    private $user;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->user = new User($this->db);
    }

    public function login($username, $password) {
        if (empty($username) || empty($password)) {
            return false;
        }
        
        $userData = $this->user->login($username, $password);
        
        if ($userData) {
            $_SESSION['user_id'] = $userData['id'];
            $_SESSION['username'] = $userData['username'];
            $_SESSION['email'] = $userData['email'];
            $_SESSION['is_admin'] = $userData['is_admin'];
            $_SESSION['login_time'] = time();
            
            session_regenerate_id(true);
            
            return true;
        }
        
        return false;
    }

   
    public function register($username, $email, $password) {

        if (empty($username) || empty($email) || empty($password)) {
            return ['success' => false, 'message' => 'All fields are required'];
        }
        \
        if (strlen($username) < 3) {
            return ['success' => false, 'message' => 'Username must be at least 3 characters'];
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Invalid email format'];
        }
        
        if (strlen($password) < 6) {
            return ['success' => false, 'message' => 'Password must be at least 6 characters'];
        }
        
        if ($this->user->usernameExists($username)) {
            return ['success' => false, 'message' => 'Username already exists'];
        }
        
        if ($this->user->emailExists($email)) {
            return ['success' => false, 'message' => 'Email already exists'];
        }
        
        if ($this->user->register($username, $email, $password)) {
            return ['success' => true, 'message' => 'Registration successful'];
        }
        
        return ['success' => false, 'message' => 'Registration failed. Please try again'];
    }

    public function logout() {
        $_SESSION = array();
        
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }
        
        session_destroy();
        
        redirect('index.php');
    }

    public function isLoggedIn() {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }

    public function isAdmin() {
        return $this->isLoggedIn() && isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;
    }

    public function getCurrentUserId() {
        return $this->isLoggedIn() ? $_SESSION['user_id'] : null;
    }

    public function getCurrentUsername() {
        return $this->isLoggedIn() ? $_SESSION['username'] : null;
    }

    public function getCurrentUserEmail() {
        return $this->isLoggedIn() ? $_SESSION['email'] : null;
    }

    public function requireLogin() {
        if (!$this->isLoggedIn()) {
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
            redirect('index.php?page=login');
        }
    }

    public function requireAdmin() {
        if (!$this->isAdmin()) {
            if (!$this->isLoggedIn()) {
                $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
                redirect('index.php?page=login');
            } else {
                redirect('index.php?page=home');
            }
        }
    }

    public function updatePassword($userId, $currentPassword, $newPassword) {
        // Validate new password
        if (strlen($newPassword) < 6) {
            return ['success' => false, 'message' => 'New password must be at least 6 characters'];
        }
        
        $user = $this->user->getById($userId);
        if (!$user || !password_verify($currentPassword, $user['password'])) {
            return ['success' => false, 'message' => 'Current password is incorrect'];
        }
        
        if ($this->user->updatePassword($userId, $newPassword)) {
            return ['success' => true, 'message' => 'Password updated successfully'];
        }
        
        return ['success' => false, 'message' => 'Failed to update password'];
    }

    public function checkSessionTimeout($timeout = 1800) {
        if ($this->isLoggedIn()) {
            if (isset($_SESSION['login_time'])) {
                $elapsed = time() - $_SESSION['login_time'];
                
                if ($elapsed > $timeout) {
                    $this->logout();
                    return false;
                }
                
                $_SESSION['login_time'] = time();
            }
        }
        
        return true;
    }

    public function getUserProfile($userId) {
        $user = $this->user->getById($userId);
        
        if ($user) {
            unset($user['password']);
            return $user;
        }
        
        return null;
    }

    public function updateProfile($userId, $email) {

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Invalid email format'];
        }
        
        if ($this->user->emailExists($email)) {
            $existingUser = $this->user->getByEmail($email);
            if ($existingUser && $existingUser['id'] != $userId) {
                return ['success' => false, 'message' => 'Email already in use'];
            }
        }
        
        if ($this->user->updateEmail($userId, $email)) {
            $_SESSION['email'] = $email;
            return ['success' => true, 'message' => 'Profile updated successfully'];
        }
        
        return ['success' => false, 'message' => 'Failed to update profile'];
    }
}
?>