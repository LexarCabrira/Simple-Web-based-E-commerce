<?php
session_start();

// SECURITY CHECK
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 'admin') {
    header("Location: login.php");
    exit();
}

include('includes/db_connect.php');

$msg = "";
$msg_type = "";

// --- ADD USER LOGIC ---
if (isset($_POST['add_user_btn'])) {
    // Capture data from form
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $password = $_POST['password']; 
    $role = mysqli_real_escape_string($conn, $_POST['role']);

    // 1. FIXED: Check if email already exists (Using 'email' column as shown in your screenshot)
    $check_email = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
    
    if (mysqli_num_rows($check_email) > 0) {
        $msg = "Error: This email is already registered.";
        $msg_type = "danger";
    } else {
        // 2. Hash the password for security
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // 3. FIXED: INSERT QUERY (Matched to your screenshot: fullname, email, phone, password, role)
        $query = "INSERT INTO users (fullname, email, phone, password, role) 
                  VALUES ('$name', '$email', '$phone', '$hashed_password', '$role')";

        if (mysqli_query($conn, $query)) {
            $msg = "Success! Account for <b>$name</b> has been created.";
            $msg_type = "success";
        } else {
            // Shows exact DB error if it fails
            $msg = "Database Error: " . mysqli_error($conn);
            $msg_type = "danger";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Silverlocks | Add Account</title>
    
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

        /* Main Content */
        .main-content { margin-left: 320px; padding: 40px 50px; flex-grow: 1; width: calc(100% - 320px); }
        .header-title { font-family: 'Playfair Display', serif; font-size: 2.5rem; }

        .glass-card {
            background: var(--glass-bg); backdrop-filter: blur(12px); border-radius: 28px;
            padding: 50px; border: 1px solid rgba(255, 255, 255, 0.5);
            box-shadow: 0 8px 32px rgba(0,0,0,0.05); max-width: 1000px; width: 100%;
        }

        /* Form Inputs */
        label { font-weight: 600; color: #555; margin-bottom: 10px; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px; }
        .form-control, .form-select {
            border-radius: 14px; padding: 14px; background: #f9f9f9; border: 1px solid #eee; font-size: 1rem;
        }
        .form-control:focus, .form-select:focus { box-shadow: none; border-color: #111; background: #fff; }

        .btn-save {
            background: #111; color: white; border: none; padding: 15px;
            border-radius: 14px; font-weight: 600; width: 100%; transition: 0.3s;
        }
        .btn-save:hover { background: #333; transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0,0,0,0.1); }

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
            <a class="nav-link active" href="admin_users.php"><i class="fas fa-user-friends"></i> Staff</a>
            <div class="mt-auto">
                <a class="nav-link text-danger" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#logoutModal">
                    <i class="fas fa-power-off"></i> Logout
                </a>
            </div>
        </nav>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-5">
            <h1 class="header-title fw-bold">Add New Account</h1>
            <a href="admin_users.php" class="btn btn-outline-dark rounded-pill px-4 py-2">Back to List</a>
        </div>

        <!-- ALERT BOX -->
        <?php if($msg != ""): ?>
            <div class="alert alert-<?php echo $msg_type; ?> rounded-4 border-0 shadow-sm mb-4 p-3"><?php echo $msg; ?></div>
        <?php endif; ?>

        <!-- REGISTRATION FORM -->
        <form action="admin_add_user.php" method="POST" class="glass-card mx-auto shadow-sm">
            <div class="row">
                <div class="col-md-6 mb-4">
                    <label>Full Name</label>
                    <input type="text" name="name" class="form-control" placeholder="Enter user's name" required>
                </div>
                <div class="col-md-6 mb-4">
                    <label>Email Address</label>
                    <input type="email" name="email" class="form-control" placeholder="email@silverlocks.com" required>
                </div>
                <div class="col-md-6 mb-4">
                    <label>Contact Number</label>
                    <input type="text" name="phone" class="form-control" placeholder="09xxxxxxxxx" required>
                </div>
                <div class="col-md-6 mb-4">
                    <label>Assign Role</label>
                    <select name="role" class="form-select" required>
                        <option value="customer">Customer</option>
                        <option value="admin">Administrator / Staff</option>
                    </select>
                </div>
                <div class="col-12 mb-5">
                    <label>Initial Password</label>
                    <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                    <small class="text-muted mt-2 d-block">Passwords are automatically encrypted for security.</small>
                </div>
            </div>
            
            <button type="submit" name="add_user_btn" class="btn-save">
                <i class="fas fa-user-plus me-2"></i> Create Account
            </button>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>