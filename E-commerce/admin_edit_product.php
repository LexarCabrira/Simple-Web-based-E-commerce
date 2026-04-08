<?php
session_start();
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 'admin') {
    header("Location: login.php");
    exit();
}
include('includes/db_connect.php');

$msg = "";

// 1. GET THE PRODUCT DETAILS BASED ON ID
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $edit_query = mysqli_query($conn, "SELECT * FROM products WHERE id = '$id'");
    $product = mysqli_fetch_assoc($edit_query);
} else {
    header("Location: admin_products.php");
}

// 2. HANDLE THE UPDATE LOGIC
if (isset($_POST['update_product'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    
    // Check if a new image is uploaded
    $new_image = $_FILES['image']['name'];
    $old_image = $_POST['old_image'];

    if ($new_image != "") {
        $update_filename = time() . '_' . $new_image;
        move_uploaded_file($_FILES['image']['tmp_name'], "uploads/" . $update_filename);
    } else {
        $update_filename = $old_image; // Keep the old image if no new one is chosen
    }

    $update_sql = "UPDATE products SET 
                   name='$name', 
                   category='$category', 
                   price='$price', 
                   stock='$stock', 
                   description='$description', 
                   image='$update_filename' 
                   WHERE id='$id'";

    if (mysqli_query($conn, $update_sql)) {
        header("Location: admin_products.php?msg=updated");
    } else {
        $msg = "Error updating: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Product | Silverlocks</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { 
            --glass-bg: rgba(255, 255, 255, 0.85);
            --sidebar-dark: #121416; 
            --text-main: #1a1a1a;
        }
        
        body { 
            font-family: 'Poppins', sans-serif; 
            background: radial-gradient(circle at top right, #f8f9fa, #e9ecef); 
            min-height: 100vh; 
            margin: 0; 
            display: flex;
        }

        /* --- FIXED SIDEBAR (Image 2 style) --- */
        .sidebar {
            width: 280px;
            height: 95vh;
            background: var(--sidebar-dark);
            position: fixed;
            left: 20px;
            top: 2.5vh;
            border-radius: 24px;
            color: white;
            z-index: 1000;
            box-shadow: 20px 20px 60px #bebebe, -20px -20px 60px #ffffff;
            display: flex;
            flex-direction: column;
            padding: 30px 10px;
        }

        .sidebar-brand {
            font-family: 'Playfair Display', serif;
            font-size: 1.6rem;
            text-align: center;
            color: white;
            text-decoration: none;
            margin-bottom: 50px;
            letter-spacing: 2px;
        }

        .nav-link {
            color: #909090 !important;
            padding: 14px 25px;
            margin: 4px 15px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            gap: 15px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            font-size: 0.95rem;
        }
        .nav-link i { font-size: 1.1rem; width: 25px; }

        .nav-link:hover, .nav-link.active {
            background: #ffffff;   /* Pure white active state */
            color: var(--sidebar-dark) !important;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            transform: none;       /* Keep stable */
        }

        /* --- HUGE MAIN CONTENT AREA --- */
        .main-content { 
            margin-left: 320px; 
            padding: 40px 50px; 
            flex-grow: 1; /* Allows it to take all remaining space */
            width: calc(100% - 320px);
        }

        /* Making the card "Huge" / Wider */
        .glass-card { 
            background: var(--glass-bg); 
            backdrop-filter: blur(12px); 
            border-radius: 28px; 
            padding: 50px; /* Increased internal padding */
            border: 1px solid rgba(255, 255, 255, 0.5); 
            box-shadow: 0 10px 40px rgba(0,0,0,0.04);
            max-width: 1400px; /* Limits it just enough for high-res screens */
            width: 100%;
        }

        .form-control, .form-select { 
            border-radius: 14px; 
            padding: 14px; 
            background: #f9f9f9; 
            border: 1px solid #eee; 
            font-size: 1rem;
        }
        
        .form-control:focus {
            background: #fff;
            border-color: #111;
            box-shadow: none;
        }

        .btn-save { 
            background: #111; 
            color: white; 
            border: none; 
            padding: 16px 40px; 
            border-radius: 14px; 
            font-weight: 600; 
            width: 100%; 
            transition: 0.3s;
        }
        
        .btn-save:hover { background: #333; transform: translateY(-2px); }

        .current-img-preview { 
            width: 100%; 
            border-radius: 20px; 
            margin-bottom: 20px; 
            border: 1px solid #eee; 
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }

        label {
            font-weight: 500;
            color: #666;
            margin-bottom: 10px;
            display: block;
        }
    </style>
</head>
<body>

    <!-- SIDEBAR -->
    <aside class="sidebar">
        <a href="admin_dashboard.php" class="sidebar-brand">SILVERLOCKS</a>
        <nav class="nav flex-column">
            <a class="nav-link" href="admin_dashboard.php"><i class="fas fa-columns"></i> Overview</a>
            <a class="nav-link" href="admin_orders.php"><i class="fas fa-receipt"></i> Orders</a>
            <a class="nav-link active" href="admin_products.php"><i class="fas fa-cookie-bite"></i> Products</a>
            <a class="nav-link" href="admin_carousel.php"><i class="fas fa-layer-group"></i> Carousel</a>
            <a class="nav-link" href="#"><i class="fas fa-user-shield"></i> Staff</a>
            <div class="mt-auto">
                <a class="nav-link text-danger" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#logoutModal">
                    <i class="fas fa-power-off"></i> Logout
                </a>
            </div>
        </nav>
    </aside>

    <!-- HUGE MAIN CONTENT -->
    <main class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-5">
            <h1 class="fw-bold m-0" style="font-family: 'Playfair Display'; font-size: 2.8rem;">Edit Product: <?php echo $product['name']; ?></h1>
            <a href="admin_products.php" class="btn btn-outline-dark rounded-pill px-4 py-2">Cancel</a>
        </div>

        <form action="" method="POST" enctype="multipart/form-data" class="glass-card">
            <input type="hidden" name="old_image" value="<?php echo $product['image']; ?>">
            
            <div class="row g-5">
                <!-- Left Column -->
                <div class="col-lg-7">
                    <div class="mb-4">
                        <label>Product Name</label>
                        <input type="text" name="name" class="form-control" value="<?php echo $product['name']; ?>" required>
                    </div>
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label>Category</label>
                            <select name="category" class="form-select">
                                <option <?php if($product['category'] == 'Cakes') echo 'selected'; ?>>Cakes</option>
                                <option <?php if($product['category'] == 'Muffins') echo 'selected'; ?>>Muffins</option>
                                <option <?php if($product['category'] == 'Breads') echo 'selected'; ?>>Breads</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label>Price (₱)</label>
                            <input type="number" step="0.01" name="price" class="form-control" value="<?php echo $product['price']; ?>" required>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label>Stock Quantity</label>
                        <input type="number" name="stock" class="form-control" value="<?php echo $product['stock']; ?>" required>
                    </div>
                    <div class="mb-0">
                        <label>Description</label>
                        <textarea name="description" class="form-control" rows="6"><?php echo $product['description']; ?></textarea>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="col-lg-5">
                    <label>Current Product Image</label>
                    <img src="uploads/<?php echo $product['image']; ?>" class="current-img-preview">
                    
                    <label>Replace Image (Optional)</label>
                    <input type="file" name="image" class="form-control" accept="image/*">
                    
                    <button type="submit" name="update_product" class="btn-save mt-5 shadow">
                        <i class="fas fa-sync-alt me-2"></i> Update Product
                    </button>
                </div>
            </div>
        </form>
    </main>
    <div class="modal fade" id="logoutModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(15px);">
                <div class="modal-body text-center p-5">
                    <div class="mb-4">
                        <i class="fas fa-sign-out-alt fa-4x text-danger opacity-75"></i>
                    </div>
                    <h3 class="fw-bold mb-3" style="font-family: 'Playfair Display';">Confirm Logout</h3>
                    <p class="text-muted">Are you sure you want to log out from the Silverlocks Admin Portal?</p>
                    
                    <div class="d-flex justify-content-center gap-3 mt-5">
                        <button type="button" class="btn btn-light rounded-pill px-4 py-2 border shadow-sm" data-bs-dismiss="modal">Cancel</button>
                        <a href="logout.php" class="btn btn-dark rounded-pill px-5 py-2 shadow-sm fw-bold">Yes, Logout</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>