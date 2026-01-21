<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Star Tech - Online Computer Store</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="nav-brand">
                <h1>Star Tech</h1>
            </div>
            <ul class="nav-menu">
                <li><a href="index.php?page=home">Home</a></li>
                <li><a href="index.php?page=products">Products</a></li>
                <li><a href="index.php?page=build-pc">Build PC</a></li>
                <?php if (isAdmin()): ?>
                    <li><a href="index.php?page=admin">Admin</a></li>
                <?php endif; ?>
                <?php if (isLoggedIn()): ?>
                    <li><a href="index.php?page=logout">Logout (<?php echo $_SESSION['username']; ?>)</a></li>
                <?php else: ?>
                    <li><a href="index.php?page=login">Login</a></li>
                    <li><a href="index.php?page=signup">Sign Up</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>
    <main class="main-content">