<?php
/**
 * AuthController
 * Handles user authentication and authorization
 * Manages login, logout, registration, and session management
 */

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

    /**
     * User login
     * @param string $username Username
     * @param string $password Password
     * @return bool Login success status
     */
    public function login($username, $password) {
        // Validate inputs
        if (empty($username) || empty($password)) {
            return false;
        }
        
        // Attempt login
        $userData = $this->user->login($username, $password);
        
        if ($userData) {
            // Set session variables
            $_SESSION['user_id'] = $userData['id'];
            $_SESSION['username'] = $userData['username'];
            $_SESSION['email'] = $userData['email'];
            $_SESSION['is_admin'] = $userData['is_admin'];
            $_SESSION['login_time'] = time();
            
            // Regenerate session ID for security
            session_regenerate_id(true);
            
            return true;
        }
        
        return false;
    }

    /**
     * User registration
     * @param string $username Username
     * @param string $email Email address
     * @param string $password Password
     * @return array Registration result with success status and message
     */
    public function register($username, $email, $password) {
        // Validate inputs
        if (empty($username) || empty($email) || empty($password)) {
            return ['success' => false, 'message' => 'All fields are required'];
        }
        
        // Check username length
        if (strlen($username) < 3) {
            return ['success' => false, 'message' => 'Username must be at least 3 characters'];
        }
        
        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Invalid email format'];
        }
        
        // Check password length
        if (strlen($password) < 6) {
            return ['success' => false, 'message' => 'Password must be at least 6 characters'];
        }
        
        // Check if username exists
        if ($this->user->usernameExists($username)) {
            return ['success' => false, 'message' => 'Username already exists'];
        }
        
        // Check if email exists
        if ($this->user->emailExists($email)) {
            return ['success' => false, 'message' => 'Email already exists'];
        }
        
        // Attempt registration
        if ($this->user->register($username, $email, $password)) {
            return ['success' => true, 'message' => 'Registration successful'];
        }
        
        return ['success' => false, 'message' => 'Registration failed. Please try again'];
    }

    /**
     * User logout
     * Destroys session and redirects to home page
     */
    public function logout() {
        // Unset all session variables
        $_SESSION = array();
        
        // Destroy session cookie
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }
        
        // Destroy session
        session_destroy();
        
        // Redirect to home page
        redirect('index.php');
    }

    /**
     * Check if user is logged in
     * @return bool Login status
     */
    public function isLoggedIn() {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }

    /**
     * Check if user is admin
     * @return bool Admin status
     */
    public function isAdmin() {
        return $this->isLoggedIn() && isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;
    }

    /**
     * Get current user ID
     * @return int|null User ID or null if not logged in
     */
    public function getCurrentUserId() {
        return $this->isLoggedIn() ? $_SESSION['user_id'] : null;
    }

    /**
     * Get current username
     * @return string|null Username or null if not logged in
     */
    public function getCurrentUsername() {
        return $this->isLoggedIn() ? $_SESSION['username'] : null;
    }

    /**
     * Get current user email
     * @return string|null Email or null if not logged in
     */
    public function getCurrentUserEmail() {
        return $this->isLoggedIn() ? $_SESSION['email'] : null;
    }

    /**
     * Require login for protected pages
     * Redirects to login page if not logged in
     */
    public function requireLogin() {
        if (!$this->isLoggedIn()) {
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
            redirect('index.php?page=login');
        }
    }

    /**
     * Require admin access
     * Redirects to login page if not admin
     */
    public function requireAdmin() {
        if (!$this->isAdmin()) {
            if (!$this->isLoggedIn()) {
                $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
                redirect('index.php?page=login');
            } else {
                // Logged in but not admin
                redirect('index.php?page=home');
            }
        }
    }

    /**
     * Update user password
     * @param int $userId User ID
     * @param string $currentPassword Current password
     * @param string $newPassword New password
     * @return array Update result
     */
    public function updatePassword($userId, $currentPassword, $newPassword) {
        // Validate new password
        if (strlen($newPassword) < 6) {
            return ['success' => false, 'message' => 'New password must be at least 6 characters'];
        }
        
        // Verify current password
        $user = $this->user->getById($userId);
        if (!$user || !password_verify($currentPassword, $user['password'])) {
            return ['success' => false, 'message' => 'Current password is incorrect'];
        }
        
        // Update password
        if ($this->user->updatePassword($userId, $newPassword)) {
            return ['success' => true, 'message' => 'Password updated successfully'];
        }
        
        return ['success' => false, 'message' => 'Failed to update password'];
    }

    /**
     * Check session timeout (optional security feature)
     * @param int $timeout Timeout in seconds (default 30 minutes)
     * @return bool True if session is valid, false if timed out
     */
    public function checkSessionTimeout($timeout = 1800) {
        if ($this->isLoggedIn()) {
            if (isset($_SESSION['login_time'])) {
                $elapsed = time() - $_SESSION['login_time'];
                
                if ($elapsed > $timeout) {
                    $this->logout();
                    return false;
                }
                
                // Update last activity time
                $_SESSION['login_time'] = time();
            }
        }
        
        return true;
    }

    /**
     * Get user profile information
     * @param int $userId User ID
     * @return array|null User data or null if not found
     */
    public function getUserProfile($userId) {
        $user = $this->user->getById($userId);
        
        if ($user) {
            // Remove password from returned data
            unset($user['password']);
            return $user;
        }
        
        return null;
    }

    /**
     * Update user profile
     * @param int $userId User ID
     * @param string $email New email
     * @return array Update result
     */
    public function updateProfile($userId, $email) {
        // Validate email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Invalid email format'];
        }
        
        // Check if email is taken by another user
        if ($this->user->emailExists($email)) {
            $existingUser = $this->user->getByEmail($email);
            if ($existingUser && $existingUser['id'] != $userId) {
                return ['success' => false, 'message' => 'Email already in use'];
            }
        }
        
        // Update profile
        if ($this->user->updateEmail($userId, $email)) {
            $_SESSION['email'] = $email;
            return ['success' => true, 'message' => 'Profile updated successfully'];
        }
        
        return ['success' => false, 'message' => 'Failed to update profile'];
    }
}
?>