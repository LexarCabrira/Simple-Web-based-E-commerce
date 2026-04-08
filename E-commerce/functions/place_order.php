<?php
session_start();
include('../includes/db_connect.php');

if(isset($_POST['placeOrderBtn'])) {
    $user_id = $_SESSION['user_id'];
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $landmark = mysqli_real_escape_string($conn, $_POST['landmark']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $payment_mode = mysqli_real_escape_string($conn, $_POST['payment_mode']);
    $tracking_no = "TRK-" . time() . rand(111, 999);

    // 1. Calculate Total
    $cart_query = "SELECT c.quantity, p.price, p.id as pid FROM carts c JOIN products p ON c.product_id = p.id WHERE c.user_id = '$user_id'";
    $cart_res = mysqli_query($conn, $cart_query);
    $total_price = 12.99; // Base delivery fee
    while($row = mysqli_fetch_assoc($cart_res)) {
        $total_price += ($row['price'] * $row['quantity']);
    }

    // 2. Insert into main Orders table
    $query = "INSERT INTO orders (tracking_no, user_id, name, phone, address, landmark, total_price, payment_mode) 
              VALUES ('$tracking_no', '$user_id', '$name', '$phone', '$address', '$landmark', '$total_price', '$payment_mode')";
    
    if(mysqli_query($conn, $query)) {
        $order_id = mysqli_insert_id($conn); // Get the ID of the order we just created

        // 3. Move items from Cart to Order Items
        mysqli_data_seek($cart_res, 0); // Reset result pointer
        while($item = mysqli_fetch_assoc($cart_res)) {
            $prod_id = $item['pid'];
            $qty = $item['quantity'];
            $price = $item['price'];
            mysqli_query($conn, "INSERT INTO order_items (order_id, product_id, qty, price) VALUES ('$order_id', '$prod_id', '$qty', '$price')");
        }

        // 4. Clear the User's Cart
        mysqli_query($conn, "DELETE FROM carts WHERE user_id = '$user_id'");

        header("Location: ../index.php?msg=Order Placed Successfully");
    }
}