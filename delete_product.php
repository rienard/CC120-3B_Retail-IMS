<?php
include 'db_connect.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "<script>alert('Product deleted successfully.'); window.location='view_products.php';</script>";
    } else {
        echo "<script>alert('Error deleting product.'); window.location='view_products.php';</script>";
    }
}
$conn->close();
?>
