<?php
session_start();
include 'db_connect.php';

// Redirect if user is not logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

// Function to check for duplicate products
function isDuplicateProduct($conn, $sku) {
    $stmt = $conn->prepare("SELECT id FROM products WHERE sku = ?");
    $stmt->bind_param("s", $sku);
    $stmt->execute();
    $stmt->store_result();
    return $stmt->num_rows > 0;
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $sku = $_POST['sku'];
    $price = $_POST['price'];
    $category = $_POST['category'];
    $entity = $_POST['entity'];

    if (!empty($name) && !empty($sku) && !empty($price) && !empty($category) && !empty($entity)) {
        if (!isDuplicateProduct($conn, $sku)) {
            $stmt = $conn->prepare("INSERT INTO products (name, sku, price, category, entity) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("ssdss", $name, $sku, $price, $category, $entity);

            if ($stmt->execute()) {
                echo "<script>alert('Product added successfully!'); window.location='product_entry.php';</script>";
            } else {
                echo "<script>alert('Error saving product.');</script>";
            }
        } else {
            echo "<script>alert('Duplicate product (SKU already exists)!');</script>";
        }
    } else {
        echo "<script>alert('All fields must be filled!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Entry</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to external CSS -->
</head>
<body>

<div class="container">
    <div class="back-arrow" onclick="history.back();">&#8592;</div> <!-- Back Arrow -->
    <h2>Product Entry</h2>
    <form method="POST">
        <input type="text" name="name" placeholder="Enter Product Name" required>
        <input type="text" name="sku" placeholder="Enter Product ID" required>
        <input type="number" name="price" placeholder="Price" required>
        <select name="category" required>
            <option value="" disabled selected>Category</option>
            <option value="Hand Tools">Hand Tools</option>
            <option value="Power Tools">Power Tools</option>
            <option value="Fasteners">Fasteners (Nails, Screws, Bolts)</option>
            <option value="Plumbing">Plumbing</option>
            <option value="Electrical">Electrical</option>
            <option value="Paint & Adhesives">Paint & Adhesives</option>
            <option value="Building Materials">Building Materials</option>
            <option value="Safety Equipment">Safety Equipment</option>
        </select>
        <input type="text" name="entity" placeholder="Entity" required>
        <button type="submit">Add Product</button>
    </form>
</div>

</body>
</html>
