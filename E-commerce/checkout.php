<?php 
include('includes/db_connect.php'); 
include('includes/header.php'); 

// Security: Redirect to login if not logged in
if(!isset($_SESSION['user_id'])) { 
    header("Location: login.php"); 
    exit; 
}

$user_id = $_SESSION['user_id'];

// Fetch cart items to show in the "Your Order" summary
$cart_query = "SELECT c.quantity, p.name, p.price 
               FROM carts c 
               JOIN products p ON c.product_id = p.id 
               WHERE c.user_id = '$user_id'";
$cart_result = mysqli_query($conn, $cart_query);

$subtotal = 0;
$count = mysqli_num_rows($cart_result);

// Calculate Subtotal
while($item = mysqli_fetch_assoc($cart_result)) {
    $subtotal += ($item['price'] * $item['quantity']);
}
$delivery = 12.99;
$grand_total = $subtotal + $delivery;

// If cart is empty, don't allow checkout
if($count == 0) { 
    header("Location: index.php"); 
    exit; 
}
?>

<style>
    body { background-color: #f8f9fa; }
    .checkout-container { padding: 60px 0; }
    .card-custom { background: white; border-radius: 20px; padding: 35px; border: 1px solid #eee; }
    
    .form-label { font-weight: 600; color: #555; font-size: 0.9rem; margin-bottom: 8px; }
    .form-control { 
        border-radius: 12px; padding: 12px; background: #fcfcfc; border: 1px solid #eee; margin-bottom: 20px; 
        transition: 0.3s;
    }
    .form-control:focus { border-color: #111; box-shadow: none; background: #fff; }
    
    .summary-card { background: white; border-radius: 20px; padding: 35px; position: sticky; top: 100px; box-shadow: 0 10px 30px rgba(0,0,0,0.03); }
    .btn-place-order { 
        background: #000; color: #fff; width: 100%; border-radius: 15px; padding: 18px; 
        font-weight: 700; border: none; text-transform: uppercase; letter-spacing: 1px; transition: 0.3s;
    }
    .btn-place-order:hover { background: #333; transform: translateY(-2px); }

    .payment-box { padding: 20px; border: 2px solid #f0f0f0; border-radius: 15px; background: #fafafa; }
</style>

<div class="container checkout-container">
    <!-- The form points to a process file we will create next -->
    <form action="functions/place_order.php" method="POST">
        <div class="row g-5">
            
            <!-- LEFT: Billing Details -->
            <div class="col-lg-7">
                <h2 class="fw-bold mb-4" style="font-family: 'Playfair Display';">Billing Details</h2>
                <div class="card-custom shadow-sm">
                    
                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="name" class="form-control" placeholder="Enter your name" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">Phone Number</label>
                            <input type="text" name="phone" class="form-control" placeholder="09xxxxxxxxx" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Landmark (Optional)</label>
                            <input type="text" name="landmark" class="form-control" placeholder="Near 7-Eleven">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Delivery Address</label>
                        <textarea name="address" class="form-control" rows="3" placeholder="Street Name, Brgy, City..." required></textarea>
                    </div>

                    <h4 class="fw-bold mt-4 mb-3" style="font-family: 'Playfair Display';">Payment Method</h4>
                    <div class="payment-box d-flex align-items-center">
                        <input type="radio" name="payment_mode" value="COD" checked class="form-check-input me-3" style="width:20px; height:20px;">
                        <div>
                            <span class="fw-bold d-block">Cash on Delivery (COD)</span>
                            <small class="text-muted">Pay when you receive your baked goods.</small>
                        </div>
                    </div>

                    <button type="submit" name="placeOrderBtn" class="btn-place-order mt-5 shadow">
                        Place Order (₱<?php echo number_format($grand_total, 2); ?>)
                    </button>
                </div>
            </div>

            <!-- RIGHT: Your Order Summary -->
            <div class="col-lg-5">
                <div class="summary-card">
                    <h4 class="fw-bold mb-4" style="font-family: 'Playfair Display';">Your Order</h4>
                    
                    <div class="d-flex justify-content-between mb-3 text-muted">
                        <span>Subtotal (<?php echo $count; ?> items)</span>
                        <span class="fw-bold text-dark">₱<?php echo number_format($subtotal, 2); ?></span>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-4 text-muted">
                        <span>Delivery</span>
                        <span class="fw-bold text-dark">₱<?php echo number_format($delivery, 2); ?></span>
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <h4 class="fw-bold m-0">Total</h4>
                        <h2 class="fw-bold text-primary m-0">₱<?php echo number_format($grand_total, 2); ?></h2>
                    </div>

                    <p class="text-muted small mt-4">
                        <i class="fas fa-info-circle me-1"></i> Please double-check your address before placing the order.
                    </p>
                </div>
            </div>

        </div>
    </form>
</div>

<?php include('includes/footer.php'); ?>