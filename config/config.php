<?php
session_start();

define('BASE_URL', 'http://localhost/star-tech/');
define('UPLOAD_DIR', __DIR__ . '/../assets/images/products/');

// Helper functions
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;
}

function redirect($page) {
    header("Location: " . BASE_URL . $page);
    exit();
}

function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}
?>