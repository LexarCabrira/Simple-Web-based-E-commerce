<?php
session_start();
include('includes/db_connect.php');

if (isset($_POST['login_btn'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE email = '$email' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $user_data = mysqli_fetch_assoc($result);
        
        if (password_verify($password, $user_data['password'])) {
            
            // Set Sessions
            $_SESSION['user_id'] = $user_data['id'];
            $_SESSION['user_name'] = $user_data['fullname'];
            $_SESSION['user_role'] = $user_data['role']; // This is 'admin' or 'user'

            // --- REDIRECTION LOGIC ---
            if ($_SESSION['user_role'] == 'admin') {
                // If admin, go to Dashboard
                header("Location: admin_dashboard.php");
            } else {
                // If normal user, go to Homepage
                header("Location: index.php");
            }
            exit();

        } else {
            echo "<script>alert('Invalid Password'); window.location='login.php';</script>";
        }
    } else {
        echo "<script>alert('Email not found'); window.location='login.php';</script>";
    }
}
?>