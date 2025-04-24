<?php
include 'db_connect.php';

// Check if ID is provided for editing
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Fetch the existing product details
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();

    // Check if the product exists
    if (!$product) {
        echo "<script>alert('Product not found!'); window.location.href='update_product.php';</script>";
        exit();
    }
}

// Handle the update request
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $name = trim($_POST['name']);
    $category = trim($_POST['category']);
    $price = floatval($_POST['price']);
    $quantity = intval($_POST['quantity']);
    $expiry_date = $_POST['expiry_date']; // Get expiry date from the form

    // Validate input
    if (empty($name) || empty($category) || $price <= 0 || $quantity < 0 || empty($expiry_date)) {
        echo "<script>alert('Please fill all fields correctly!');</script>";
    } else {
        // Update product details including the expiry date
        $stmt = $conn->prepare("UPDATE products SET name=?, category=?, price=?, quantity=?, expiry_date=? WHERE id=?");
        $stmt->bind_param("ssdiss", $name, $category, $price, $quantity, $expiry_date, $id);

        if ($stmt->execute()) {
            echo "<script>alert('Product updated successfully!'); window.location.href='update_product.php';</script>";
            exit();
        } else {
            echo "Error updating product: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    <link rel="stylesheet" href="styles.css"> <!-- External CSS -->
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
            text-align: center;
        }
        .container {
            width: 50%;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
        input[type="text"], input[type="number"], input[type="date"] {
            width: 90%;
            padding: 8px;
            margin: 5px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        input[disabled] {
            background-color: #eee;
        }
        button {
            background: #28a745;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
        }
        button:hover {
            background: #218838;
        }
        a {
            display: inline-block;
            margin-top: 10px;
            color: #007bff;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
        .back-arrow {
            font-size: 18px;
            cursor: pointer;
        }
    </style>
</head>
<body>

<div class="container">
    <div style="text-align: left; margin-bottom: 10px;">
        <div class="back-arrow" onclick="history.back();">&#8592;</div>
    </div>
    <h2>Edit Product</h2>
    <form method="POST">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($product['id']); ?>">

        <label>Product Name:</label><br>
        <input type="text" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required><br>

        <label>SKU</label><br>
        <input type="text" name="sku_display" value="<?php echo htmlspecialchars($product['sku']); ?>" disabled><br>

        <label>Category:</label><br>
        <input type="text" name="category" value="<?php echo htmlspecialchars($product['category']); ?>" required><br>

        <label>Price:</label><br>
        <input type="number" step="0.01" name="price" value="<?php echo htmlspecialchars($product['price']); ?>" required><br>

        <label>Quantity:</label><br>
        <input type="number" name="quantity" value="<?php echo htmlspecialchars($product['quantity']); ?>" required><br>

        <label>Expiry Date:</label><br>
        <input type="date" name="expiry_date" value="<?php echo htmlspecialchars($product['expiry_date']); ?>" required><br>

        <button type="submit" name="update">Update Product</button>
        <a href="dashboard.php">Back to Dashboard</a><br>
        <a href="update_product.php">Back to Products</a>
    </form>
</div>

</body>
</html>

<?php
$conn->close();
?>
