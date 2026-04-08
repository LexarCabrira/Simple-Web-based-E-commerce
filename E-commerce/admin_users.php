<?php
session_start();

// SECURITY CHECK
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 'admin') {
    header("Location: login.php");
    exit();
}

include('includes/db_connect.php');

// FETCH ALL USERS FROM DATABASE
$user_query = "SELECT * FROM users ORDER BY created_at DESC";
$user_result = mysqli_query($conn, $user_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Silverlocks Admin | User Management</title>
    
    <!-- Fonts & Icons -->
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
            color: var(--text-main);
            margin: 0;
            display: flex;
        }

        /* Sidebar - Elite Style */
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

        /* Main Content Area */
        .main-content { margin-left: 320px; padding: 40px 60px 40px 20px; width: 100%; }
        .header-title { font-family: 'Playfair Display', serif; font-size: 2.5rem; }

        .btn-add-account {
            background: #121416; color: white; border: none; border-radius: 14px;
            padding: 12px 24px; font-weight: 500; transition: 0.3s; text-decoration: none;
        }
        .btn-add-account:hover { background: #333; transform: translateY(-2px); color: white; }

        /* Table Card */
        .glass-card {
            background: var(--glass-bg); backdrop-filter: blur(12px); border-radius: 28px;
            padding: 40px; border: 1px solid rgba(255, 255, 255, 0.5);
            box-shadow: 0 15px 40px rgba(0,0,0,0.03); margin-top: 30px;
        }

        .table thead th {
            text-transform: uppercase; font-size: 0.75rem; letter-spacing: 1px;
            font-weight: 700; color: #aaa; border: none; padding-bottom: 20px;
        }

        .table tbody td {
            padding: 25px 0; border-bottom: 1px solid #f8f8f8; vertical-align: middle;
        }

        /* Role Badges */
        .role-badge {
            padding: 6px 16px; border-radius: 50px; font-size: 0.7rem; font-weight: 800;
            text-transform: uppercase; display: inline-block;
        }
        .role-admin { background: #1a1a1a; color: #fff; }
        .role-customer { background: #0dcaf0; color: #fff; }

        .btn-delete {
            width: 40px; height: 40px; border-radius: 50%; border: 1px solid #ffebed;
            background: #fff; color: #dc3545; transition: 0.2s; border: none; cursor: pointer;
        }
        .btn-delete:hover { background: #dc3545; color: #fff; }
        .modal-content { border-radius: 28px; border: none; }

    </style>
</head>
<body>

    <!-- SIDEBAR -->
    <aside class="sidebar">
        <a href="admin_dashboard.php" class="sidebar-brand">SILVER<span>LOCKS</span></a>
        <nav class="nav flex-column">
            <a class="nav-link" href="admin_dashboard.php"><i class="fas fa-columns"></i> Overview</a>
            <a class="nav-link" href="admin_orders.php"><i class="fas fa-receipt"></i> Orders</a>
            <a class="nav-link" href="admin_products.php"><i class="fas fa-cookie-bite"></i> Products</a>
            <a class="nav-link" href="admin_carousel.php"><i class="fas fa-layer-group"></i> Carousel</a>
            <a class="nav-link active" href="admin_users.php"><i class="fas fa-user-shield"></i> Staff</a>
            <div class="mt-auto">
                <a class="nav-link text-danger" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#logoutModal">
                    <i class="fas fa-power-off"></i> Logout
                </a>
            </div>
        </nav>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="main-content">
        
        <!-- MESSAGES -->
        <?php if(isset($_GET['msg']) && $_GET['msg'] == 'deleted'): ?>
            <div class="alert alert-dark rounded-4 shadow-sm border-0 mb-4 p-3">
                <i class="fas fa-check-circle me-2 text-success"></i> Account removed successfully.
            </div>
        <?php endif; ?>
        
        <?php if(isset($_GET['msg']) && $_GET['msg'] == 'error_self_delete'): ?>
            <div class="alert alert-danger rounded-4 shadow-sm border-0 mb-4 p-3">
                <i class="fas fa-exclamation-triangle me-2"></i> Error: You cannot delete your own admin account.
            </div>
        <?php endif; ?>

        <div class="d-flex justify-content-between align-items-end mb-5">
            <div>
                <h1 class="header-title fw-bold mb-1">User Management</h1>
                <p class="text-muted mb-0">Manage administrative staff and registered customers.</p>
            </div>
            <a href="admin_add_user.php" class="btn-add-account shadow-sm">
                <i class="fas fa-user-plus me-2"></i> Add Account
            </a>
        </div>

        <div class="glass-card">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th width="10%">ID</th>
                            <th width="35%">User</th>
                            <th width="15%" class="text-center">Role</th>
                            <th width="15%">Contact</th>
                            <th width="15%">Joined</th>
                            <th width="10%" class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if(mysqli_num_rows($user_result) > 0) {
                            while($row = mysqli_fetch_assoc($user_result)) {
                                // Logic to fix "Undefined Array Key" by checking multiple possibilities
                                $name = $row['name'] ?? $row['user_name'] ?? 'N/A';
                                $email = $row['email'] ?? $row['user_email'] ?? 'N/A';
                                $role = $row['role'] ?? $row['user_role'] ?? 'customer';
                                $phone = $row['phone'] ?? $row['user_phone'] ?? 'N/A';
                                
                                $role_class = (strtolower($role) == 'admin') ? 'role-admin' : 'role-customer';
                        ?>
                        <tr>
                            <td class="text-muted fw-bold">#<?php echo $row['id']; ?></td>
                            <td>
                                <div class="fw-bold"><?php echo htmlspecialchars($name); ?></div>
                                <div class="text-muted small"><?php echo htmlspecialchars($email); ?></div>
                            </td>
                            <td class="text-center">
                                <span class="role-badge <?php echo $role_class; ?>">
                                    <?php echo htmlspecialchars($role); ?>
                                </span>
                            </td>
                            <td class="text-muted small"><?php echo htmlspecialchars($phone); ?></td>
                            <td class="text-muted small"><?php echo date("M d, Y", strtotime($row['created_at'])); ?></td>
                            <td class="text-center">
                                <!-- REMOVE FUNCTION -->
                                <a href="admin_delete_user.php?id=<?php echo $row['id']; ?>" 
                                   class="btn-delete d-inline-flex align-items-center justify-content-center"
                                   onclick="return confirm('Remove this account permanently?')">
                                    <i class="fas fa-trash-alt"></i>
                                </a>
                            </td>
                        </tr>
                        <?php 
                            }
                        } else {
                            echo "<tr><td colspan='6' class='text-center py-5 text-muted'>No users found.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>