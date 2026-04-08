<?php
// 1. Database Connection
include('includes/db_connect.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 2. Get data and protect against SQL Injection
    $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $phone    = mysqli_real_escape_string($conn, $_POST['phone']);
    $address  = mysqli_real_escape_string($conn, $_POST['address']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // 3. Check if passwords match
    if ($password !== $confirm_password) {
        echo "<script>alert('Passwords do not match!'); window.history.back();</script>";
        exit();
    }

    // 4. Check if email already exists
    $check_email = "SELECT email FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $check_email);
    if (mysqli_num_rows($result) > 0) {
        echo "<script>alert('Email already registered! Try logging in.'); window.location='login.php';</script>";
        exit();
    }

    // 5. Hash the password for security
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // 6. Insert into Database
    // Make sure the columns (fullname, email, phone, password, address, role) match your phpMyAdmin
    $sql = "INSERT INTO users (fullname, email, phone, password, address, role) 
            VALUES ('$fullname', '$email', '$phone', '$hashed_password', '$address', 'user')";

    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Account created successfully!'); window.location='login.php';</script>";
    } else {
        // This will show you exactly what is wrong if it fails
        echo "Error: " . mysqli_error($conn);
    }
}
?>