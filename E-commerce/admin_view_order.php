<?php
session_start();
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 'admin') {
    header("Location: login.php");
    exit();
}
include('includes/db_connect.php');

// 1. FETCH ORDER DATA
if (isset($_GET['id'])) {
    $order_id = mysqli_real_escape_string($conn, $_GET['id']);
    
    // Get main order info
    $order_query = mysqli_query($conn, "SELECT * FROM orders WHERE id = '$order_id'");
    $order_data = mysqli_fetch_assoc($order_query);

    if (!$order_data) { header("Location: admin_orders.php"); exit(); }

    // Get order items joined with product info for images
    $items_query = mysqli_query($conn, "SELECT oi.*, p.name, p.image 
                                        FROM order_items oi 
                                        JOIN products p ON oi.product_id = p.id 
                                        WHERE oi.order_id = '$order_id'");
} else {
    header("Location: admin_orders.php");
    exit();
}

// 2. UPDATE STATUS LOGIC
if (isset($_POST['update_status_btn'])) {
    $new_status = $_POST['order_status'];
    mysqli_query($conn, "UPDATE orders SET status = '$new_status' WHERE id = '$order_id'");
    header("Location: admin_view_order.php?id=$order_id&msg=updated");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Silverlocks | View Order #<?php echo $order_id; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --sidebar-dark: #121416; --bg-light: #f8f9fa; }
        body { font-family: 'Poppins', sans-serif; background-color: var(--bg-light); margin: 0; display: flex; }

        /* SIDEBAR (Image Style) */
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
        .nav-link.active { background: #ffffff; color: var(--sidebar-dark) !important; }

        /* MAIN CONTENT */
        .main-content { margin-left: 320px; padding: 40px 50px; flex-grow: 1; }
        .glass-card { background: #fff; border-radius: 24px; padding: 30px; border: 1px solid #eee; box-shadow: 0 10px 30px rgba(0,0,0,0.02); margin-bottom: 25px; }

        .btn-back { color: #666; text-decoration: none; font-size: 0.9rem; margin-bottom: 15px; display: inline-block; }
        .tracking-badge { background: #111; color: #fff; padding: 5px 15px; border-radius: 8px; font-size: 0.75rem; font-weight: 600; display: inline-block; }
        
        /* TABLE STYLES */
        .item-row img { width: 50px; height: 50px; object-fit: cover; border-radius: 10px; margin-right: 15px; }
        .table th { text-transform: uppercase; font-size: 0.75rem; color: #888; border: none; padding-bottom: 15px; }
        .table td { vertical-align: middle; border-bottom: 1px solid #f8f8f8; padding: 15px 0; }

        .summary-line { display: flex; justify-content: flex-end; gap: 40px; margin-top: 15px; font-size: 0.95rem; }
        .summary-label { color: #888; width: 100px; text-align: right; }
        .summary-val { font-weight: 600; width: 100px; text-align: right; }

        .btn-update { background: #1a1a1a; color: #fff; width: 100%; border-radius: 12px; padding: 12px; font-weight: 600; border: none; }
        .form-select { border-radius: 10px; padding: 10px; background: #fcfcfc; border: 1px solid #eee; margin-bottom: 15px; }

        .customer-info-line { display: flex; align-items: flex-start; gap: 12px; margin-bottom: 12px; font-size: 0.9rem; color: #444; }
        .customer-info-line i { color: #888; margin-top: 4px; }
    </style>
</head>
<body>

    <aside class="sidebar">
        <a href="admin_dashboard.php" class="sidebar-brand text-white">SILVERLOCKS</a>
        <nav class="nav flex-column">
            <a class="nav-link" href="admin_dashboard.php"><i class="fas fa-columns"></i> Overview</a>
            <a class="nav-link" href="admin_orders.php"><i class="fas fa-receipt"></i> Orders</a>
            <a class="nav-link" href="admin_products.php"><i class="fas fa-cookie-bite"></i> Products</a>
            <a class="nav-link" href="admin_carousel.php"><i class="fas fa-layer-group"></i> Carousel</a>
            <a class="nav-link" href="#"><i class="fas fa-user-shield"></i> Staff</a>
            <div class="mt-auto">
                <a class="nav-link text-danger" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#logoutModal">
                    <i class="fas fa-power-off"></i> Logout
                </a>
            </div>
        </nav>
    </aside>

    <main class="main-content">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <a href="admin_orders.php" class="btn-back"><i class="fas fa-arrow-left me-2"></i> Back to Orders</a>
                <h1 class="fw-bold" style="font-family: 'Playfair Display';">Order #<?php echo $order_id; ?></h1>
                <div class="tracking-badge"><?php echo $order_data['tracking_no']; ?></div>
            </div>
            
        </div>

        <div class="row g-4 mt-2">
            <!-- LEFT COLUMN: Order Items -->
            <div class="col-lg-8">
                <div class="glass-card">
                    <h5 class="fw-bold mb-4">Order Items</h5>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr><th>Product</th><th class="text-center">Price</th><th class="text-center">Qty</th><th class="text-end">Total</th></tr>
                            </thead>
                            <tbody>
                                <?php 
                                $subtotal = 0;
                                while($item = mysqli_fetch_assoc($items_query)): 
                                    $item_total = $item['price'] * $item['qty'];
                                    $subtotal += $item_total;
                                ?>
                                <tr class="item-row">
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="uploads/<?php echo $item['image']; ?>" alt="">
                                            <span class="fw-semibold"><?php echo $item['name']; ?></span>
                                        </div>
                                    </td>
                                    <td class="text-center">₱<?php echo number_format($item['price'], 2); ?></td>
                                    <td class="text-center">x <?php echo $item['qty']; ?></td>
                                    <td class="text-end fw-bold">₱<?php echo number_format($item_total, 2); ?></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="summary-line"><div class="summary-label">Subtotal</div><div class="summary-val">₱<?php echo number_format($subtotal, 2); ?></div></div>
                    <div class="summary-line"><div class="summary-label">Shipping</div><div class="summary-val">₱12.99</div></div>
                    <div class="summary-line mt-3 pt-3 border-top">
                        <div class="summary-label fw-bold text-dark">Grand Total</div>
                        <div class="summary-val text-primary" style="font-size: 1.2rem;">₱<?php echo number_format($order_data['total_price'], 2); ?></div>
                    </div>
                </div>
            </div>

            <!-- RIGHT COLUMN: Actions & Customer -->
            <div class="col-lg-4">
                <div class="glass-card">
                    <h5 class="fw-bold mb-4">Update Status</h5>
                    <form action="" method="POST">
                        <label class="small text-muted mb-2">Current Status</label>
                        <select name="order_status" class="form-select">
                            <option value="Pending" <?php if($order_data['status'] == 'Pending') echo 'selected'; ?>>Pending</option>
                            <option value="Packed" <?php if($order_data['status'] == 'Packed') echo 'selected'; ?>>Packed</option>
                            <option value="Shipped" <?php if($order_data['status'] == 'Shipped') echo 'selected'; ?>>Shipped</option>
                            <option value="Delivered" <?php if($order_data['status'] == 'Delivered') echo 'selected'; ?>>Delivered</option>
                            <option value="Cancelled" <?php if($order_data['status'] == 'Cancelled') echo 'selected'; ?>>Cancelled</option>
                        </select>
                        <button type="submit" name="update_status_btn" class="btn-update shadow">Update Order</button>
                    </form>
                </div>

                <div class="glass-card">
                    <h5 class="fw-bold mb-4">Customer Details</h5>
                    <div class="customer-info-line"><i class="fas fa-user"></i> <span><?php echo $order_data['name']; ?></span></div>
                    <div class="customer-info-line"><i class="fas fa-envelope"></i> <span>revillajamesandrei4@gmail.com</span></div> <!-- Update with user email if available -->
                    <div class="customer-info-line"><i class="fas fa-phone"></i> <span><?php echo $order_data['phone']; ?></span></div>
                    
                    <hr class="my-4 opacity-25">
                    
                    <label class="small text-muted mb-2 text-uppercase fw-bold">Shipping Address</label>
                    <div class="customer-info-line">
                        <i class="fas fa-map-marker-alt text-primary"></i> 
                        <span class="small"><?php echo $order_data['address']; ?><br><b>Landmark:</b> <?php echo $order_data['landmark']; ?></span>
                    </div>
                </div>
            </div>
        </div>
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