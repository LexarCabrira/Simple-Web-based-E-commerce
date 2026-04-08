<?php
session_start();
// Security check
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 'admin') {
    header("Location: login.php");
    exit();
}

// 1. Database Connection
include('includes/db_connect.php'); 

$msg = "";
$msg_type = "";

// 2. THE SAVE FUNCTION
if (isset($_POST['save_product'])) {
    // Sanitize inputs to prevent SQL injection
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $stock = mysqli_real_escape_string($conn, $_POST['stock']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);

    // Image Upload Logic
    $image_name = $_FILES['image']['name'];
    $image_tmp = $_FILES['image']['tmp_name'];
    $image_ext = pathinfo($image_name, PATHINFO_EXTENSION);
    
    // Rename image to prevent overwriting (e.g., 1704283200_cake.jpg)
    $new_image_name = time() . '_' . str_replace(' ', '_', $image_name);
    $upload_path = "uploads/" . $new_image_name;

    // Validate if fields are not empty
    if (!empty($name) && !empty($price) && !empty($image_name)) {
        
        // SQL Query to Insert
        $sql = "INSERT INTO products (name, category, price, stock, description, image) 
                VALUES ('$name', '$category', '$price', '$stock', '$description', '$new_image_name')";

        if (mysqli_query($conn, $sql)) {
            // Move file to the 'uploads' folder
            if (move_uploaded_file($image_tmp, $upload_path)) {
                $msg = "Success! Product has been added to the database.";
                $msg_type = "success";
            } else {
                $msg = "Product saved, but image failed to upload. Check 'uploads' folder permissions.";
                $msg_type = "warning";
            }
        } else {
            $msg = "Database Error: " . mysqli_error($conn);
            $msg_type = "danger";
        }
    } else {
        $msg = "Please fill in all required fields.";
        $msg_type = "danger";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Silverlocks | Add Product</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --glass-bg: rgba(255, 255, 255, 0.85);
            --sidebar-dark: #121416;
            --silver-accent: #C0C0C0;
            --silver-gradient: linear-gradient(145deg, #e6e6e6, #ffffff);
            --charcoal: #2c2f33;
            --text-main: #1a1a1a;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: radial-gradient(circle at top right, #f8f9fa, #e9ecef);
            min-height: 100vh;
            color: var(--text-main);
        }

        /* Sidebar */
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

        .sidebar-brand span { color: var(--silver-accent); }

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
            background: var(--silver-gradient);
            color: var(--sidebar-dark) !important;
            transform: translateX(4px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }

        /* Main Content */
        .main-content {
            margin-left: 320px;
            padding: 40px 40px 40px 20px;
        }

        .glass-card {
            background: var(--glass-bg); backdrop-filter: blur(12px); border-radius: 24px;
            padding: 40px; border: 1px solid rgba(255, 255, 255, 0.5); box-shadow: 0 8px 32px rgba(0,0,0,0.05);
        }

        .form-control, .form-select { border-radius: 12px; padding: 12px; background: #f9f9f9; border: 1px solid #eee; }
        .form-control:focus { box-shadow: none; border-color: #111; background: #fff; }

        .upload-box {
            border: 2px dashed #ddd; border-radius: 20px; padding: 30px; text-align: center; background: #fcfcfc;
        }

        .img-preview { max-width: 100%; max-height: 200px; border-radius: 15px; display: none; margin: 0 auto 15px; }

        .btn-save { background: #111; color: white; border: none; padding: 12px 40px; border-radius: 12px; font-weight: 600; width: 100%; }
        .btn-save:hover { background: #333; }
        .modal-content { border-radius: 28px; border: none; }
    </style>
</head>
<body>

    <aside class="sidebar">
        <a href="admin_dashboard.php" class="sidebar-brand">SILVER<span>LOCKS</span></a>
         <nav class="nav flex-column">
            <a class="nav-link" href="admin_dashboard.php"><i class="fas fa-columns"></i> Overview</a>
            <a class="nav-link" href="admin_orders.php"><i class="fas fa-receipt"></i> Orders</a>
            <a class="nav-link active" href="admin_products.php"><i class="fas fa-cookie-bite"></i> Products</a>
            <a class="nav-link" href="admin_carousel.php"><i class="fas fa-layer-group"></i> Carousel</a>
            <a class="nav-link" href="admin_user.php"><i class="fas fa-user-shield"></i> Staff</a>
            <div class="mt-auto">
                <a class="nav-link text-danger" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#logoutModal">
                    <i class="fas fa-power-off"></i> Logout
                </a>
            </div>
        </nav>
    </aside>

    <main class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-5">
            <h1 class="fw-bold" style="font-family: 'Playfair Display';">Add New Product</h1>
            <a href="admin_products.php" class="btn btn-outline-dark rounded-pill px-4">Back</a>
        </div>

        <!-- Feedback Message -->
        <?php if($msg != ""): ?>
            <div class="alert alert-<?php echo $msg_type; ?> rounded-4 border-0 shadow-sm mb-4"><?php echo $msg; ?></div>
        <?php endif; ?>

        <form action="" method="POST" enctype="multipart/form-data" class="glass-card">
            <div class="row g-5">
                <div class="col-lg-7">
                    <div class="mb-4">
                        <label class="form-label fw-medium">Product Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Category</label>
                            <select name="category" class="form-select">
                                <option>Cakes</option>
                                <option>Cupcakes</option>
                                <option>Muffins</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Price (₱)</label>
                            <input type="number" step="0.01" name="price" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-medium">Stock Quantity</label>
                        <input type="number" name="stock" class="form-control" required>
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-medium">Description</label>
                        <textarea name="description" class="form-control" rows="4"></textarea>
                    </div>
                </div>

                <div class="col-lg-5">
                    <label class="form-label fw-medium">Product Image</label>
                    <div class="upload-box">
                        <img id="preview" class="img-preview" src="#">
                        <div id="placeholder">
                            <i class="fas fa-cloud-upload-alt fa-3x mb-2 text-muted"></i>
                            <p class="text-muted small">Upload PNG or JPG</p>
                        </div>
                        <input type="file" name="image" id="imgInput" class="form-control mt-2" accept="image/*" required>
                    </div>
                    <button type="submit" name="save_product" class="btn-save mt-5 shadow-sm">
                        <i class="fas fa-save me-2"></i> Save to Database
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
    <script>
        const imgInput = document.getElementById('imgInput');
        const preview = document.getElementById('preview');
        const placeholder = document.getElementById('placeholder');

        imgInput.onchange = evt => {
            const [file] = imgInput.files;
            if (file) {
                preview.src = URL.createObjectURL(file);
                preview.style.display = 'block';
                placeholder.style.display = 'none';
            }
        }
    </script>
</body>
</html>