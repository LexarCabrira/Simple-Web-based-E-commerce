<?php
session_start();
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 'admin') {
    header("Location: login.php");
    exit();
}
include('includes/db_connect.php');

// Fetch products
$product_query = "SELECT * FROM products ORDER BY created_at DESC";
$product_result = mysqli_query($conn, $product_query);

// Fetch Stats
$total_count = mysqli_num_rows($product_result);
$low_stock_query = mysqli_query($conn, "SELECT COUNT(*) as low FROM products WHERE stock < 10");
$low_stock_data = mysqli_fetch_assoc($low_stock_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Silverlocks Admin | Products</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --glass-bg: rgba(255, 255, 255, 0.85); --sidebar-dark: #121416; --text-main: #1a1a1a; }
        body { font-family: 'Poppins', sans-serif; background: radial-gradient(circle at top right, #f8f9fa, #e9ecef); min-height: 100vh; color: var(--text-main); margin: 0; }

        /* FIXED SIDEBAR LAYOUT */
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
            background: #ffffff; color: var(--sidebar-dark) !important; box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        }

        .main-content { margin-left: 320px; padding: 40px 40px 40px 20px; }
        .glass-card {
            background: var(--glass-bg); backdrop-filter: blur(12px); border-radius: 24px;
            padding: 35px; border: 1px solid rgba(255, 255, 255, 0.5); box-shadow: 0 8px 32px rgba(0,0,0,0.05);
        }
        .stat-widget { background: white; border-radius: 20px; padding: 20px; display: flex; align-items: center; gap: 20px; }
        .icon-box { width: 55px; height: 55px; border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; }
        
        .product-img { width: 60px; height: 60px; object-fit: cover; border-radius: 12px; }
        .table thead th { text-transform: uppercase; font-size: 0.75rem; color: #888; border: none; padding: 15px; text-align: center; }
        .table tbody td { padding: 15px; border-bottom: 1px solid rgba(0,0,0,0.03); vertical-align: middle; text-align: center; }
        .btn-add { background: var(--sidebar-dark); border: none; border-radius: 14px; padding: 12px 24px; color: #fff; text-decoration: none; }

        /* MODAL STYLING */
        .modal-content { border-radius: 24px; border: none; padding: 20px; }
        .btn-confirm-delete { background: #121416; color: white; border-radius: 12px; padding: 10px 25px; }
        .btn-confirm-delete:hover { background: #dc3545; color: white; }
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
            <a class="nav-link" href="admin_users.php"><i class="fas fa-user-shield"></i> Staff</a>
            <div class="mt-auto">
                <a class="nav-link text-danger" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#logoutModal">
                    <i class="fas fa-power-off"></i> Logout
                </a>
            </div>
        </nav>
    </aside>

    <main class="main-content">
        <div class="d-flex justify-content-between align-items-end mb-5">
            <div>
                <h1 class="fw-bold mb-1" style="font-family: 'Playfair Display'; font-size: 2.5rem;">Products Management</h1>
                <p class="text-muted mb-0">Manage your product inventory and pricing</p>
            </div>
            <a href="admin_add_products.php" class="btn-add shadow-sm"><i class="fas fa-plus me-2"></i> Add Product</a>
        </div>

        <div class="row g-4 mb-5">
            <div class="col-xl-3 col-md-6">
                <div class="stat-widget shadow-sm">
                    <div class="icon-box bg-info bg-opacity-10 text-info"><i class="fas fa-box"></i></div>
                    <div><p class="text-muted small mb-0">Total Products</p><h4 class="fw-bold mb-0"><?php echo $total_count; ?></h4></div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="stat-widget shadow-sm">
                    <div class="icon-box bg-warning bg-opacity-10 text-warning"><i class="fas fa-exclamation-triangle"></i></div>
                    <div><p class="text-muted small mb-0">Low Stock</p><h4 class="fw-bold mb-0"><?php echo $low_stock_data['low']; ?></h4></div>
                </div>
            </div>
        </div>

        <div class="glass-card">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr><th>Image</th><th>Name</th><th>Category</th><th>Price</th><th>Stock</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                        <?php while($product = mysqli_fetch_assoc($product_result)): ?>
                        <tr>
                            <td><img src="uploads/<?php echo $product['image']; ?>" class="product-img"></td>
                            <td class="fw-semibold"><?php echo $product['name']; ?></td>
                            <td><span class="badge bg-light text-dark border"><?php echo $product['category']; ?></span></td>
                            <td class="fw-bold">₱<?php echo number_format($product['price'], 2); ?></td>
                            <td><span class="fw-bold <?php echo ($product['stock'] < 10) ? 'text-danger' : 'text-success'; ?>"><?php echo $product['stock']; ?></span></td>
                            <td>
                                <a href="admin_edit_product.php?id=<?php echo $product['id']; ?>" class="btn btn-sm btn-light border">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <!-- Triggering the Modal instead of browser confirm -->
                                <button type="button" class="btn btn-sm btn-light border text-danger" onclick="confirmDelete(<?php echo $product['id']; ?>)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- DELETE CONFIRMATION MODAL -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow-lg">
                <div class="modal-body text-center py-4">
                    <div class="mb-3 text-danger">
                        <i class="fas fa-exclamation-circle fa-4x"></i>
                    </div>
                    <h4 class="fw-bold" style="font-family: 'Playfair Display';">Are you sure?</h4>
                    <p class="text-muted">This product will be permanently deleted from the database. This action cannot be undone.</p>
                    <div class="mt-4">
                        <button type="button" class="btn btn-light rounded-pill px-4 me-2" data-bs-dismiss="modal">Cancel</button>
                        <a id="deleteConfirmBtn" href="#" class="btn btn-confirm-delete rounded-pill px-4">Yes, Delete</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Function to pass ID to the modal and show it
        function confirmDelete(id) {
            const deleteUrl = 'admin_delete_product.php?id=' + id;
            document.getElementById('deleteConfirmBtn').setAttribute('href', deleteUrl);
            
            var myModal = new bootstrap.Modal(document.getElementById('deleteModal'));
            myModal.show();
        }
    </script>
</body>
</html>