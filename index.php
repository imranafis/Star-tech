<?php
require_once 'config/config.php';

$page = isset($_GET['page']) ? $_GET['page'] : 'home';

include 'includes/header.php';

switch($page) {
    case 'home':
        include 'views/home.php';
        break;
    case 'products':
        include 'views/products.php';
        break;
    case 'build-pc':
        include 'views/build-pc.php';
        break;
    case 'checkout':
        include 'views/checkout.php';
        break;
    case 'login':
        include 'views/login.php';
        break;
    case 'signup':
        include 'views/signup.php';
        break;
    case 'admin':
        if (isAdmin()) {
            include 'views/admin/dashboard.php';
        } else {
            redirect('index.php?page=login');
        }
        break;
    case 'add-product':
        if (isAdmin()) {
            include 'views/admin/add-product.php';
        } else {
            redirect('index.php?page=login');
        }
        break;
    case 'edit-product':
        if (isAdmin()) {
            include 'views/admin/edit-product.php';
        } else {
            redirect('index.php?page=login');
        }
        break;
    case 'logout':
        session_destroy();
        redirect('index.php');
        break;
    default:
        include 'views/home.php';
}

include 'includes/footer.php';
?>