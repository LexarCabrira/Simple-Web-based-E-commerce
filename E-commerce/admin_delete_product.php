<?php
session_start();

// Security Check
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 'admin') {
    header("Location: login.php");
    exit();
}

include('includes/db_connect.php');

if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);

    // 1. Get the image filename first so we can delete it from the folder
    $query = mysqli_query($conn, "SELECT image FROM products WHERE id = '$id'");
    $data = mysqli_fetch_assoc($query);
    $image_filename = $data['image'];

    // 2. Delete the record from the database
    $delete_query = "DELETE FROM products WHERE id = '$id'";

    if (mysqli_query($conn, $delete_query)) {
        // 3. If database delete is successful, remove the file from 'uploads' folder
        $file_path = "uploads/" . $image_filename;
        if (file_exists($file_path)) {
            unlink($file_path); // This deletes the actual file
        }
        
        // Redirect back with success message
        header("Location: admin_products.php?msg=deleted");
        exit();
    } else {
        echo "Error deleting record: " . mysqli_error($conn);
    }
} else {
    header("Location: admin_products.php");
    exit();
}
?>