<?php 
include('includes/db_connect.php'); 
include('includes/header.php'); 

// 1. Fetch Carousel Slides (Always show carousel)
$carousel_query = "SELECT c.*, p.image as p_image, p.id as p_id 
                   FROM carousel c 
                   JOIN products p ON c.product_id = p.id 
                   ORDER BY c.created_at DESC";
$carousel_result = mysqli_query($conn, $carousel_query);

// 2. FILTERING LOGIC (Controlled by Header Links)
$category_filter = isset($_GET['category']) ? mysqli_real_escape_string($conn, $_GET['category']) : 'All';

if ($category_filter == 'All' || empty($category_filter)) {
    $product_query = "SELECT * FROM products ORDER BY created_at DESC";
    $display_title = "Our Full Collection";
} else {
    // Matches database values: Cakes, Cupcakes, Muffins
    $product_query = "SELECT * FROM products WHERE category = '$category_filter' ORDER BY created_at DESC";
    $display_title = "Our Handcrafted " . $category_filter;
}
$product_result = mysqli_query($conn, $product_query);
?>

<style>
    .hero-section { background: #f8f9fa; border-bottom: 1px solid #eee; }
    .product-card { border: none; border-radius: 25px; transition: 0.3s; background: #fff; overflow: hidden; }
    .product-card:hover { transform: translateY(-10px); box-shadow: 0 20px 40px rgba(0,0,0,0.05); }
    .btn-main { background: #111; color: #fff; border-radius: 50px; padding: 10px 20px; font-weight: 600; border: none; transition: 0.3s; }
    .btn-main:hover { background: #333; color: #fff; }
</style>

<!-- HERO SECTION (DYNAMIC CAROUSEL) -->
<section class="hero-section">
    <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">
            <?php 
            $active = "active"; 
            if (mysqli_num_rows($carousel_result) > 0):
                while($slide = mysqli_fetch_assoc($carousel_result)):
            ?>
                <div class="carousel-item <?php echo $active; ?>">
                    <div class="container py-5">
                        <div class="row align-items-center py-5">
                            <div class="col-md-6">
                                <?php if(!empty($slide['badge_text'])): ?>
                                    <span class="badge rounded-pill mb-3" style="background:#111; padding: 10px 20px;"><?php echo strtoupper($slide['badge_text']); ?></span>
                                <?php endif; ?>
                                <h1 class="display-3 fw-bold mb-4"><?php echo $slide['hero_title']; ?></h1>
                                <p class="lead text-muted mb-5"><?php echo $slide['description']; ?></p>
                                <a href="details.php?id=<?php echo $slide['p_id']; ?>" class="btn-main px-5 py-3">View Product <i class="fas fa-arrow-right ms-2"></i></a>
                            </div>
                            <div class="col-md-6 text-center">
                                <img src="uploads/<?php echo $slide['p_image']; ?>" class="img-fluid rounded-4 shadow-lg" style="height: 480px; width: 100%; object-fit: cover; border-radius: 30px !important;">
                            </div>
                        </div>
                    </div>
                </div>
            <?php $active = ""; endwhile; endif; ?>
        </div>
    </div>
</section>

<!-- PRODUCT SECTION -->
<section class="py-5 mt-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 style="font-family:'Playfair Display'; font-weight:700; font-size: 3rem;"><?php echo $display_title; ?></h2>
            <p class="text-muted">Discover our artisanal treats baked with love.</p>
        </div>

        <div class="row g-4">
            <?php
            if (mysqli_num_rows($product_result) > 0) {
                while($item = mysqli_fetch_assoc($product_result)) {
            ?>
            <div class="col-md-4">
                <div class="card product-card h-100 shadow-sm">
                    <img src="uploads/<?php echo $item['image']; ?>" class="card-img-top" style="height: 300px; object-fit: cover;">
                    <div class="card-body p-4 text-center">
                        <span class="text-muted small text-uppercase fw-bold"><?php echo $item['category']; ?></span>
                        <h5 class="fw-bold mt-2 mb-3"><?php echo $item['name']; ?></h5>
                        <div style="font-weight:700; font-size:1.2rem;" class="mb-4">₱<?php echo number_format($item['price'], 2); ?></div>
                        <a href="details.php?id=<?php echo $item['id']; ?>" class="btn-main w-100 py-2" style="text-decoration:none">View Details</a>
                    </div>
                </div>
            </div>
            <?php 
                } 
            } else {
                echo "<div class='col-12 text-center py-5'><h3 class='text-muted'>No items found in this section.</h3></div>";
            }
            ?>
        </div>
    </div>
</section>

<?php include('includes/footer.php'); ?>