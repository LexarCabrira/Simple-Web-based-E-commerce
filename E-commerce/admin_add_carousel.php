<?php
session_start();
include('includes/db_connect.php');

// Fetch all products for the dropdown
$products_query = mysqli_query($conn, "SELECT id, name FROM products ORDER BY name ASC");

if (isset($_POST['add_to_carousel'])) {
    $p_id = $_POST['product_id'];
    $badge = mysqli_real_escape_string($conn, $_POST['badge']);
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $desc = mysqli_real_escape_string($conn, $_POST['description']);

    $sql = "INSERT INTO carousel (product_id, badge_text, hero_title, description) VALUES ('$p_id', '$badge', '$title', '$desc')";
    if (mysqli_query($conn, $sql)) {
        header("Location: admin_carousel.php");
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Silverlocks | Add Carousel</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --glass-bg: rgba(255, 255, 255, 0.85); --sidebar-dark: #121416; }
        body { font-family: 'Poppins', sans-serif; background: radial-gradient(circle at top right, #f8f9fa, #e9ecef); min-height: 100vh; margin: 0; display: flex; }
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
        .nav-link.active { background: #ffffff; color: var(--sidebar-dark) !important; box-shadow: 0 4px 20px rgba(0,0,0,0.15); }
        .main-content { margin-left: 320px; padding: 40px 50px; flex-grow: 1; }
        .glass-card { background: var(--glass-bg); backdrop-filter: blur(12px); border-radius: 24px; padding: 50px; border: 1px solid rgba(255, 255, 255, 0.5); max-width: 800px; }
        .form-control, .form-select { border-radius: 12px; padding: 12px; background: #f9f9f9; border: 1px solid #eee; }
        .btn-save { background: #111; color: white; border: none; padding: 15px; border-radius: 12px; font-weight: 600; width: 100%; transition: 0.3s; }
        .btn-save:hover { background: #333; transform: translateY(-2px); }
        .modal-content { border-radius: 28px; border: none; }
    </style>
</head>
<body>
    <aside class="sidebar">
        <div class="sidebar-brand">SILVERLOCKS</div>
        <nav class="nav flex-column">
            <a class="nav-link" href="admin_dashboard.php"><i class="fas fa-columns"></i> Overview</a>
            <a class="nav-link" href="admin_orders.php"><i class="fas fa-receipt"></i> Orders</a>
            <a class="nav-link" href="admin_products.php"><i class="fas fa-cookie-bite"></i> Products</a>
            <a class="nav-link active" href="admin_carousel.php"><i class="fas fa-layer-group"></i> Carousel</a>
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
            <h1 class="fw-bold m-0" style="font-family: 'Playfair Display'; font-size: 2.5rem;">Add Carousel Slide</h1>
            <a href="admin_carousel.php" class="btn btn-outline-dark rounded-pill px-4">Back</a>
        </div>

        <form action="" method="POST" class="glass-card mx-auto">
            <div class="mb-4">
                <label class="form-label fw-bold">Select Product to Feature</label>
                <select name="product_id" class="form-select" required>
                    <option value="" disabled selected>-- Choose a Product --</option>
                    <?php while($p = mysqli_fetch_assoc($products_query)): ?>
                        <option value="<?php echo $p['id']; ?>"><?php echo $p['name']; ?></option>
                    <?php endwhile; ?>
                </select>
                <small class="text-muted mt-2 d-block small">The image will be taken from this product automatically.</small>
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold">Badge Text</label>
                <input type="text" name="badge" class="form-control" placeholder="e.g. NEW ARRIVAL, HOT SALE">
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold">Hero Title</label>
                <input type="text" name="title" class="form-control" placeholder="e.g. RED VELVET SPECIAL" required>
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold">Subtitle / Description</label>
                <textarea name="description" class="form-control" rows="4" placeholder="Brief descriptive text..."></textarea>
            </div>

            <button type="submit" name="add_to_carousel" class="btn-save mt-3 shadow-sm">Add to Carousel</button>
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