<?php 
include('includes/db_connect.php'); 
include('includes/header.php'); 

if(!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }
$user_id = $_SESSION['user_id'];

$cart_query = "SELECT c.id as cid, c.quantity, p.name, p.price, p.image 
               FROM carts c JOIN products p ON c.product_id = p.id 
               WHERE c.user_id = '$user_id'";
$cart_result = mysqli_query($conn, $cart_query);
?>

<style>
    body { background-color: #f8f9fa; }
    .cart-container { padding: 60px 0; }
    .cart-card { background: white; border-radius: 25px; padding: 30px; box-shadow: 0 10px 30px rgba(0,0,0,0.02); }
    .cart-item { display: flex; align-items: center; padding: 25px 0; border-bottom: 1px solid #f8f8f8; }
    .cart-img { width: 90px; height: 90px; object-fit: contain; background: #fcfcfc; border-radius: 15px; border: 1px solid #eee; }
    
    /* Qty Buttons */
    .qty-btn { width: 32px; height: 32px; border-radius: 50%; border: 1px solid #ddd; background: #fff; display: flex; align-items: center; justify-content: center; text-decoration: none; color: #333; font-weight: bold; }
    .qty-btn:hover { background: #000; color: #fff; }
    
    .summary-card { background: white; border-radius: 25px; padding: 35px; position: sticky; top: 110px; box-shadow: 0 15px 40px rgba(0,0,0,0.04); }
    .btn-checkout { background: #111; color: white; border-radius: 50px; padding: 16px; width: 100%; border: none; font-weight: 600; transition: 0.3s; }
    .btn-checkout:hover { background: #333; transform: translateY(-3px); }
</style>

<div class="container cart-container">
    <div class="row g-5">
        <div class="col-lg-8">
            <h2 class="fw-bold mb-4" style="font-family: 'Playfair Display'; font-size: 2.5rem;">Your Cart</h2>
            
            <div class="cart-card">
                <?php if(mysqli_num_rows($cart_result) > 0): ?>
                    <?php while($item = mysqli_fetch_assoc($cart_result)): ?>
                        <!-- CART ITEM -->
                        <div class="cart-item product_data">
                            <!-- Checkbox for auto-calculate -->
                            <input type="checkbox" class="form-check-input product-check me-4" checked 
                                   data-price="<?php echo $item['price']; ?>" 
                                   data-qty="<?php echo $item['quantity']; ?>"
                                   style="width: 22px; height: 22px; cursor:pointer;">
                            
                            <img src="uploads/<?php echo $item['image']; ?>" class="cart-img">
                            
                            <div class="ms-4 flex-grow-1">
                                <h5 class="fw-bold mb-1"><?php echo $item['name']; ?></h5>
                                <p class="text-muted small mb-0">₱<?php echo number_format($item['price'], 2); ?></p>
                                <a href="functions/handle_cart.php?remove_id=<?php echo $item['cid']; ?>" 
                                   class="text-danger x-small text-decoration-none" 
                                   onclick="return confirm('Remove item?')">Remove</a>
                            </div>

                            <div class="text-end">
                                <div class="d-flex align-items-center justify-content-end mb-2 gap-2">
                                    <a href="functions/handle_cart.php?id=<?php echo $item['cid']; ?>&update_qty=dec" class="qty-btn">−</a>
                                    <span class="fw-bold px-2"><?php echo $item['quantity']; ?></span>
                                    <a href="functions/handle_cart.php?id=<?php echo $item['cid']; ?>&update_qty=inc" class="qty-btn">+</a>
                                </div>
                                <div class="fw-bold">₱<?php echo number_format($item['price'] * $item['quantity'], 2); ?></div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="text-center py-5 text-muted">Your cart is empty.</div>
                <?php endif; ?>
            </div>
        </div>

        <!-- ORDER SUMMARY -->
        <div class="col-lg-4">
            <div class="summary-card">
                <h4 class="fw-bold mb-4" style="font-family: 'Playfair Display';">Order Summary</h4>
                
                <div class="d-flex justify-content-between mb-3">
                    <span class="text-muted">Subtotal</span>
                    <span class="fw-bold" id="subtotal_display">₱0.00</span>
                </div>
                <div class="d-flex justify-content-between mb-4">
                    <span class="text-muted">Delivery</span>
                    <span class="fw-bold">₱12.99</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between align-items-center mb-5 mt-4">
                    <h4 class="fw-bold m-0">Total</h4>
                    <h3 class="fw-bold text-primary m-0" id="total_display">₱0.00</h3>
                </div>

                <a href="checkout.php" class="btn-checkout shadow text-center text-decoration-none d-block">
                    Proceed to Checkout
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    // AUTO-CALCULATE FUNCTION
    function updateCartTotal() {
        let subtotal = 0;
        let delivery = 12.99;
        
        // Loop through all CHECKED checkboxes
        document.querySelectorAll('.product-check:checked').forEach(checkbox => {
            let price = parseFloat(checkbox.getAttribute('data-price'));
            let qty = parseInt(checkbox.getAttribute('data-qty'));
            subtotal += (price * qty);
        });

        // Update UI
        document.getElementById('subtotal_display').innerText = "₱" + subtotal.toLocaleString(undefined, {minimumFractionDigits: 2});
        
        let grandTotal = subtotal > 0 ? subtotal + delivery : 0;
        document.getElementById('total_display').innerText = "₱" + grandTotal.toLocaleString(undefined, {minimumFractionDigits: 2});
    }

    // Attach event listeners to all checkboxes
    document.querySelectorAll('.product-check').forEach(checkbox => {
        checkbox.addEventListener('change', updateCartTotal);
    });

    // Run once on page load
    window.onload = updateCartTotal;
</script>

<?php include('includes/footer.php'); ?>