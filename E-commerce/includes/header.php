<?php 
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include('includes/db_connect.php'); 

// Determine which page or category is currently active for the CSS styling
$current_page = basename($_SERVER['PHP_SELF']); 
$active_category = isset($_GET['category']) ? $_GET['category'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Silverlocks | Artisanal Bakery</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root { --primary-silver: #9da1a5; --dark-text: #1a1a1a; }
        body { font-family: 'Poppins', sans-serif; color: var(--dark-text); }
        h1, h2, h3, .navbar-brand { font-family: 'Playfair Display', serif; }
        .navbar { background: rgba(255, 255, 255, 0.98); border-bottom: 1px solid #eee; padding: 15px 0; }
        .navbar-brand { font-size: 1.8rem; font-weight: 700; letter-spacing: 1px; color: var(--dark-text) !important; }
        .navbar-brand span { color: var(--primary-silver); }
        .nav-link { font-weight: 500; color: var(--dark-text) !important; transition: 0.3s; padding: 10px 15px !important; }
        .nav-link:hover, .nav-link.active { color: var(--primary-silver) !important; }
        .btn-dashboard { background: var(--dark-text); color: white; border-radius: 50px; padding: 6px 20px; font-size: 0.8rem; text-decoration: none; }
        .btn-logout { border: 1px solid #ff4d4d; color: #ff4d4d; border-radius: 50px; padding: 5px 20px; font-size: 0.8rem; text-decoration: none; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg sticky-top">
    <div class="container">
        <a class="navbar-brand" href="index.php">SILVER<span>LOCKS</span></a>
        
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mx-auto gap-2">
                <!-- HOME -->
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page == 'index.php' && $active_category == '') ? 'active' : ''; ?>" href="index.php">Home</a>
                </li>

                <!-- CAKES -->
                <li class="nav-item">
                    <a class="nav-link <?php echo ($active_category == 'Cakes') ? 'active' : ''; ?>" href="index.php?category=Cakes">Cakes</a>
                </li>

                <!-- CUPCAKES -->
                <li class="nav-item">
                    <a class="nav-link <?php echo ($active_category == 'Cupcakes') ? 'active' : ''; ?>" href="index.php?category=Cupcakes">Cupcakes</a>
                </li>

                <!-- MUFFINS -->
                <li class="nav-item">
                    <a class="nav-link <?php echo ($active_category == 'Muffins') ? 'active' : ''; ?>" href="index.php?category=Muffins">Muffins</a>
                </li>
            </ul>

            <div class="d-flex align-items-center gap-3">
                <a href="cart.php" class="text-dark me-2"><i class="fas fa-shopping-bag fa-lg"></i></a>
                <?php if(isset($_SESSION['user_id'])): ?>
                    <?php if($_SESSION['user_role'] == 'admin'): ?>
                        <a href="admin_dashboard.php" class="btn-dashboard">Dashboard</a>
                    <?php endif; ?>
                    <a href="logout.php" class="btn-logout">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-dark btn-sm rounded-pill px-4">Login</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>