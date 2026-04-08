<?php
session_start();

// SECURITY CHECK
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 'admin') {
    header("Location: login.php");
    exit();
}

include('includes/db_connect.php');

// --- 1. FETCH REAL-TIME STATS ---

// Total Sales (Delivered only)
$sales_res = mysqli_query($conn, "SELECT SUM(total_price) as total FROM orders WHERE status='Delivered'");
$sales_row = mysqli_fetch_assoc($sales_res);
$total_sales = number_format($sales_row['total'] ?? 0, 2);

// Total Orders Count
$order_res = mysqli_query($conn, "SELECT COUNT(*) as total FROM orders");
$order_row = mysqli_fetch_assoc($order_res);
$total_orders = $order_row['total'] ?? 0;

// In Process (Pending or Packed)
$proc_res = mysqli_query($conn, "SELECT COUNT(*) as total FROM orders WHERE status IN ('Pending', 'Packed')");
$proc_row = mysqli_fetch_assoc($proc_res);
$in_process = $proc_row['total'] ?? 0;

// Low Stock Alert (Products with stock < 10)
$stock_res = mysqli_query($conn, "SELECT COUNT(*) as total FROM products WHERE stock < 10");
$stock_row = mysqli_fetch_assoc($stock_res);
$low_stock_count = $stock_row['total'] ?? 0;

// --- 2. FETCH RECENT 5 TRANSACTIONS ---
$recent_orders_query = "SELECT * FROM orders ORDER BY created_at DESC LIMIT 5";
$recent_result = mysqli_query($conn, $recent_orders_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Silverlocks Admin | Elite Dashboard</title>
    
    <!-- Fonts & Icons -->
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
            background: #ffffff; color: var(--sidebar-dark) !important; transform: none;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        }

        .main-content { margin-left: 320px; padding: 40px 40px 40px 20px; }

        .glass-card {
            background: var(--glass-bg); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.5);
            border-radius: 24px; padding: 35px; box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.07);
        }

        .filter-select {
            appearance: none; background-color: #f1f3f5; border: none; border-radius: 50px;
            padding: 8px 45px 8px 20px; font-size: 0.85rem; font-weight: 500; color: #333; cursor: pointer; outline: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='%23666' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
            background-repeat: no-repeat; background-position: right 15px center; transition: all 0.2s;
        }

        .stat-widget { background: white; border-radius: 20px; padding: 20px; display: flex; align-items: center; gap: 20px; transition: 0.3s; }
        .stat-icon-box { width: 55px; height: 55px; border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; }
        
        .icon-bg-sales { background: #E8F5E9; color: #2E7D32; }
        .icon-bg-orders { background: #E3F2FD; color: #1565C0; }
        .icon-bg-pending { background: #FFFDE7; color: #F9A825; }
        .icon-bg-stock { background: #FFEBEE; color: #C62828; }

        /* Table Status Pills */
        .status-pill { padding: 6px 14px; border-radius: 10px; font-size: 0.75rem; font-weight: 600; display: inline-block;}
        .status-pending { background: #FFFDE7; color: #F9A825; }
        .status-delivered { background: #E8F5E9; color: #2E7D32; }
        .status-packed { background: #E3F2FD; color: #1565C0; }

        .table thead th { text-transform: uppercase; font-size: 0.75rem; font-weight: 700; color: #888; border: none; padding: 15px; }
        .table tbody td { padding: 20px 15px; border-bottom: 1px solid rgba(0,0,0,0.03); vertical-align: middle; }
        
        .btn-action-manage { background: white; border: 1px solid #ddd; border-radius: 12px; padding: 6px 15px; font-size: 0.85rem; transition: 0.2s; text-decoration: none; color: black; }
        .btn-action-manage:hover { background: #000; color: #fff; }

        /* MODAL STYLES */
        .modal-content { border-radius: 28px; border: none; }
    </style>
</head>
<body>

    <div class="sidebar">
        <a href="admin_dashboard.php" class="sidebar-brand">SILVER<span>LOCKS</span></a>
        <nav class="nav flex-column">
            <a class="nav-link active" href="admin_dashboard.php"><i class="fas fa-columns"></i> Overview</a>
            <a class="nav-link" href="admin_orders.php"><i class="fas fa-receipt"></i> Orders</a>
            <a class="nav-link" href="admin_products.php"><i class="fas fa-cookie-bite"></i> Products</a>
            <a class="nav-link" href="admin_carousel.php"><i class="fas fa-layer-group"></i> Carousel</a>
            <a class="nav-link" href="admin_users.php"><i class="fas fa-user-shield"></i> Staff</a>
            
            <!-- UPDATE: Modal Trigger for Logout -->
            <div class="mt-auto">
                <a class="nav-link text-danger" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#logoutModal">
                    <i class="fas fa-power-off"></i> Logout
                </a>
            </div>
        </nav>
    </div>

    <div class="main-content">
        <div class="d-flex justify-content-between align-items-end mb-5">
            <div>
                <h1 class="fw-bold mb-1" style="font-family: 'Playfair Display'; font-size: 2.5rem;">Elite Dashboard</h1>
                <p class="text-muted mb-0">Analytics for Silverlocks Bakery • <?php echo date("M j, Y"); ?></p>
            </div>
            <a href="admin_report.php" target="_blank" class="btn btn-dark rounded-pill px-4 py-2 shadow-sm" style="font-size: 0.9rem; text-decoration:none;">
                <i class="fas fa-print me-2"></i> Download Report
            </a>
        </div>

        <div class="row g-4 mb-5">
            <div class="col-xl-3 col-md-6"><div class="stat-widget shadow-sm"><div class="stat-icon-box icon-bg-sales"><i class="fas fa-wallet"></i></div><div><p class="text-muted small mb-0">Total Sales</p><h4 class="fw-bold mb-0">₱<?php echo $total_sales; ?></h4></div></div></div>
            <div class="col-xl-3 col-md-6"><div class="stat-widget shadow-sm"><div class="stat-icon-box icon-bg-orders"><i class="fas fa-shopping-cart"></i></div><div><p class="text-muted small mb-0">Total Orders</p><h4 class="fw-bold mb-0"><?php echo $total_orders; ?></h4></div></div></div>
            <div class="col-xl-3 col-md-6"><div class="stat-widget shadow-sm"><div class="stat-icon-box icon-bg-pending"><i class="fas fa-hourglass-half"></i></div><div><p class="text-muted small mb-0">Pending</p><h4 class="fw-bold mb-0"><?php echo $in_process; ?></h4></div></div></div>
            <div class="col-xl-3 col-md-6"><div class="stat-widget shadow-sm"><div class="stat-icon-box icon-bg-stock"><i class="fas fa-exclamation-triangle"></i></div><div><p class="text-muted small mb-0">Low Stock</p><h4 class="fw-bold mb-0"><?php echo $low_stock_count; ?> Items</h4></div></div></div>
        </div>

        <div class="glass-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold mb-0" style="font-family: 'Playfair Display'; font-size: 1.4rem;">Recent Transactions</h5>
            </div>
            
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Transaction</th>
                            <th>Recipient</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if(mysqli_num_rows($recent_result) > 0) {
                            while($item = mysqli_fetch_assoc($recent_result)) {
                                $status_class = "status-" . strtolower($item['status']);
                                $initial = strtoupper(substr($item['name'], 0, 1));
                        ?>
                        <tr>
                            <td>
                                <div class="fw-bold text-primary" style="font-size: 0.9rem;"><?php echo $item['tracking_no']; ?></div>
                                <div class="text-muted small"><?php echo date("M d, Y", strtotime($item['created_at'])); ?></div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div style="width: 35px; height: 35px; background: #eee; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 0.8rem;"><?php echo $initial; ?></div>
                                    <div>
                                        <div class="fw-bold mb-0" style="font-size: 0.9rem;"><?php echo $item['name']; ?></div>
                                        <div class="text-muted small"><?php echo $item['phone']; ?></div>
                                    </div>
                                </div>
                            </td>
                            <td><span class="fw-bold">₱<?php echo number_format($item['total_price'], 2); ?></span></td>
                            <td><span class="status-pill <?php echo $status_class; ?>"><?php echo $item['status']; ?></span></td>
                            <td>
                                <a href="admin_view_order.php?id=<?php echo $item['id']; ?>" class="btn-action-manage">Manage</a>
                            </td>
                        </tr>
                        <?php 
                            }
                        } else {
                            echo "<tr><td colspan='5' class='text-center py-5 text-muted'>No recent orders to display.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- LOGOUT MODAL SECTION -->
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
</body>
</html>