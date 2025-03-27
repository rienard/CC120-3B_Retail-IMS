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
    $category = $_POST['category'];
    $brand = $_POST['brand'];
    $supplier_id = $_POST['supplier'];
    $sku = $_POST['sku'];
    $purchase_price = $_POST['purchase_price'];
    $selling_price = $_POST['selling_price'];
    $stock_quantity = $_POST['stock_quantity'];
    $reorder_level = $_POST['reorder_level'];
    $expiration_date = $_POST['expiration_date'];

    $image = $_FILES['image']['name'];
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($image);
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Allowed file types
    $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];

    if (!empty($name) && !empty($category) && !empty($sku) && !empty($purchase_price) && !empty($selling_price) && !empty($stock_quantity)) {
        if (!isDuplicateProduct($conn, $sku)) {
            // Upload image if valid
            if (!empty($image) && in_array($imageFileType, $allowed_types)) {
                move_uploaded_file($_FILES['image']['tmp_name'], $target_file);
            } else {
                $target_file = null; // No image uploaded or invalid file
            }

            $stmt = $conn->prepare("INSERT INTO products 
                (name, category, brand, supplier_id, sku, purchase_price, selling_price, stock_quantity, reorder_level, expiration_date, image) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssissddiss", $name, $category, $brand, $supplier_id, $sku, $purchase_price, $selling_price, $stock_quantity, $reorder_level, $expiration_date, $target_file);
            
            if ($stmt->execute()) {
                echo "<script>alert('Product added successfully.'); window.location='product_entry.php';</script>";
            } else {
                echo "<script>alert('Error saving product.');</script>";
            }
        } else {
            echo "<script>alert('Duplicate product (SKU already exists)!');</script>";
        }
    } else {
        echo "<script>alert('All required fields must be filled!');</script>";
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
        <h2>Product Entry Form</h2>
        <form method="POST" enctype="multipart/form-data">
            <label>Product Name:</label>
            <input type="text" name="name" required><br>

            <label>Category:</label>
            <input type="text" name="category" required><br>

            <label>Brand:</label>
            <input type="text" name="brand"><br>

            <label>Supplier:</label>
            <select name="supplier">
                <option value="1">Supplier A</option>
                <option value="2">Supplier B</option>
            </select><br>

            <label>SKU:</label>
            <input type="text" name="sku" required><br>

            <label>Purchase Price:</label>
            <input type="number" step="0.01" name="purchase_price" required><br>

            <label>Selling Price:</label>
            <input type="number" step="0.01" name="selling_price" required><br>

            <label>Stock Quantity:</label>
            <input type="number" name="stock_quantity" required><br>

            <label>Reorder Level:</label>
            <input type="number" name="reorder_level"><br>

            <label>Expiration Date:</label>
            <input type="date" name="expiration_date"><br>

            <label>Product Image:</label>
            <input type="file" name="image" accept="image/*"><br>

            <button type="submit">Submit</button>
        </form>
    </div>
</body>
</html>
