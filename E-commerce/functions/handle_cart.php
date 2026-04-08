<?php
session_start();
include('../includes/db_connect.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// --- ACTION: ADD TO CART ---
if (isset($_POST['add_to_cart_btn'])) {
    $product_id = mysqli_real_escape_string($conn, $_POST['product_id']);
    $qty = mysqli_real_escape_string($conn, $_POST['product_qty']);

    $check_cart = mysqli_query($conn, "SELECT * FROM carts WHERE user_id='$user_id' AND product_id='$product_id'");

    if (mysqli_num_rows($check_cart) > 0) {
        mysqli_query($conn, "UPDATE carts SET quantity = quantity + $qty WHERE user_id='$user_id' AND product_id='$product_id'");
    } else {
        mysqli_query($conn, "INSERT INTO carts (user_id, product_id, quantity) VALUES ('$user_id', '$product_id', '$qty')");
    }
    header("Location: ../cart.php");
}

// --- ACTION: UPDATE QUANTITY (Increment/Decrement) ---
if (isset($_GET['update_qty'])) {
    $cart_id = mysqli_real_escape_string($conn, $_GET['id']);
    $scope = $_GET['update_qty']; // 'inc' or 'dec'

    if ($scope == "inc") {
        mysqli_query($conn, "UPDATE carts SET quantity = quantity + 1 WHERE id='$cart_id' AND user_id='$user_id'");
    } else {
        mysqli_query($conn, "UPDATE carts SET quantity = IF(quantity > 1, quantity - 1, 1) WHERE id='$cart_id' AND user_id='$user_id'");
    }
    header("Location: ../cart.php");
}

// --- ACTION: REMOVE ITEM ---
if (isset($_GET['remove_id'])) {
    $cart_id = mysqli_real_escape_string($conn, $_GET['remove_id']);
    mysqli_query($conn, "DELETE FROM carts WHERE id='$cart_id' AND user_id='$user_id'");
    header("Location: ../cart.php");
}
?>