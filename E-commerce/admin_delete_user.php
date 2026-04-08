<?php
session_start();

// 1. Security Check
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 'admin') {
    header("Location: login.php");
    exit();
}

include('includes/db_connect.php');

if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    $current_admin_id = $_SESSION['user_id'];

    // 2. Prevent the Admin from deleting their own account
    if ($id == $current_admin_id) {
        header("Location: admin_users.php?msg=error_self_delete");
        exit();
    }

    // 3. Delete the user
    $query = "DELETE FROM users WHERE id = '$id'";

    if (mysqli_query($conn, $query)) {
        header("Location: admin_users.php?msg=deleted");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
} else {
    header("Location: admin_users.php");
    exit();
}
?>