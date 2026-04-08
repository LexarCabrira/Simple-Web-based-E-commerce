<?php 
include('includes/db_connect.php'); 
include('includes/header.php'); 

// 1. Get Product ID from URL
if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    
    // 2. Fetch specific product from your XAMPP database
    $query = "SELECT * FROM products WHERE id = '$id' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $item = mysqli_fetch_assoc($result);
    } else {
        echo "<div class='container py-5 text-center'><h1 style='font-family:Playfair Display'>Product Not Found</h1><a href='index.php' class='btn btn-dark rounded-pill px-4'>Back to Home</a></div>";
        include('includes/footer.php');
        exit;
    }
} else {
    header("Location: index.php");
    exit;
}
?>

<style>
    :root { --silver-focus: #9da1a5; }
    body { background-color: #fcfcfc; }

    /* HUGE CONTENT LAYOUT */
    .details-section { 
        padding: 80px 0; 
        min-height: 85vh;
    }

    /* The SneakPeak-style Tilted Image Holder */
    .product-img-container {
        background-color: #f3f3f3;
        border-radius: 40px;
        padding: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        overflow: hidden;
        min-height: 600px; /* Huge Size */
    }

    .product-img-container img {
        max-width: 100%;
        height: 450px;
        object-fit: contain;
        transform: rotate(-10deg); /* The Signature Tilt */
        transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        filter: drop-shadow(0 30px 50px rgba(0,0,0,0.12));
    }

    .product-img-container:hover img {
        transform: rotate(0deg) scale(1.05);
    }

    .product-title {
        font-family: 'Playfair Display', serif;
        font-weight: 700;
        font-size: 3.5rem; /* Huge Typography */
        color: #1a1a1a;
        margin-bottom: 15px;
    }

    .price-large {
        font-size: 2.2rem;
        font-weight: 700;
        color: #1a1a1a;
        margin-bottom: 30px;
    }

    .description-text {
        color: #777;
        line-height: 1.8;
        font-size: 1.1rem;
        margin-bottom: 40px;
    }

    /* Quantity Controls */
    .qty-wrapper {
        display: flex;
        align-items: center;
        background: white;
        border: 1px solid #eee;
        border-radius: 50px;
        width: fit-content;
        padding: 10px 25px;
        margin-bottom: 30px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.02);
    }

    .qty-btn { background: none; border: none; font-size: 1.5rem; cursor: pointer; padding: 0 10px; }
    .qty-val { width: 60px; text-align: center; border: none; font-weight: 700; font-size: 1.2rem; background: transparent; }

    /* Action Box Styles */
    .login-box {
        background: white;
        border: 1px solid #f0f0f0;
        border-radius: 25px;
        padding: 30px;
        display: flex;
        align-items: center;
        gap: 20px;
        box-shadow: 0 15px 40px rgba(0,0,0,0.04);
    }

    .btn-add-cart {
        background: #111;
        color: #fff;
        border: none;
        border-radius: 50px;
        padding: 18px 40px;
        font-weight: 600;
        font-size: 1.1rem;
        width: 100%;
        transition: 0.3s;
    }
    .btn-add-cart:hover { background: #333; transform: translateY(-3px); box-shadow: 0 10px 20px rgba(0,0,0,0.15); }

    .breadcrumb-item a { color: #aaa; text-decoration: none; font-size: 0.95rem; }
</style>

<div class="container details-section">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-5">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
            <li class="breadcrumb-item active text-dark fw-bold"><?php echo $item['name']; ?></li>
        </ol>
    </nav>

    <div class="row align-items-center g-5">
        <!-- LEFT: HUGE TILTED IMAGE -->
        <div class="col-lg-6">
            <div class="product-img-container">
                <img src="uploads/<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>">
            </div>
        </div>

        <!-- RIGHT: PRODUCT INFO -->
        <div class="col-lg-6 ps-lg-5">
            <span class="text-muted text-uppercase fw-bold letter-spacing-2 small"><?php echo $item['category']; ?></span>
            <h1 class="product-title"><?php echo $item['name']; ?></h1>
            <div class="price-large">₱<?php echo number_format($item['price'], 2); ?></div>

            <p class="description-text">
                <?php echo nl2br($item['description']); ?>
            </p>

            <?php if($item['stock'] > 0): ?>
                
                <!-- START OF ADD TO CART FORM -->
                <form action="functions/handle_cart.php" method="POST">
                    <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                    
                    <div class="order-controls">
                        <p class="small text-muted mb-2 fw-bold">SELECT QUANTITY (Available: <?php echo $item['stock']; ?>)</p>
                        <div class="qty-wrapper">
                            <button type="button" class="qty-btn" onclick="updateQty(-1)">−</button>
                            <input type="text" name="product_qty" id="qty" class="qty-val" value="1" readonly>
                            <button type="button" class="qty-btn" onclick="updateQty(1)">+</button>
                        </div>
                    </div>

                    <!-- FIX: SESSION CHECK FOR LOGIN -->
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <button type="submit" name="add_to_cart_btn" class="btn-add-cart shadow-lg">
                            Add to Cart <i class="fas fa-shopping-bag ms-2"></i>
                        </button>
                    <?php else: ?>
                        <!-- GUEST VIEW: Locked Box -->
                        <div class="login-box">
                            <div style="width:50px; height:50px; background:#f8f8f8; border-radius:50%; display:flex; align-items:center; justify-content:center;">
                                <i class="fas fa-lock"></i>
                            </div>
                            <div>
                                <p class="mb-0 text-muted small">This item is exclusive for members.</p>
                                <a href="login.php" class="text-dark fw-bold text-decoration-none">Login to Purchase</a>
                            </div>
                        </div>
                    <?php endif; ?>
                </form>

            <?php else: ?>
                <div class="alert alert-danger rounded-4 p-4 border-0 shadow-sm">
                    <i class="fas fa-info-circle me-2"></i> This product is currently out of stock.
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    function updateQty(change) {
        let input = document.getElementById('qty');
        let val = parseInt(input.value);
        let maxStock = <?php echo $item['stock']; ?>;
        val += change;
        if (val < 1) val = 1;
        if (val > maxStock) val = maxStock;
        input.value = val;
    }
</script>

<?php include('includes/footer.php'); ?>