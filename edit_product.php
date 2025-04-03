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
        echo "<script>alert('Product not found!'); window.location.href='index.php';</script>";
        exit();
    }
}

// Handle the update request
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $name = trim($_POST['name']);
    $sku = trim($_POST['sku']);
    $category = trim($_POST['category']);
    $price = floatval($_POST['price']);
    $entity = intval($_POST['entity']);

    // Ensure all fields are filled
    if (empty($name) || empty($sku) || empty($category) || $price <= 0 || $entity < 0) {
        echo "<script>alert('Please fill all fields correctly!');</script>";
    } else {
        // Prepare the update query
        $stmt = $conn->prepare("UPDATE products SET name=?, sku=?, category=?, price=?, entity=? WHERE id=?");
        $stmt->bind_param("sssddi", $name, $sku, $category, $price, $entity, $id);

        if ($stmt->execute()) {
            echo "<script>alert('Product updated successfully!'); window.location.href='index.php';</script>";
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
        input[type="text"], input[type="number"] {
            width: 90%;
            padding: 8px;
            margin: 5px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
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
        <input type="text" name="name" value="<?php echo htmlspecialchars($product['name']); ?>"><br>

        <label>SKU:</label><br>
        <input type="text" name="sku" value="<?php echo htmlspecialchars($product['sku']); ?>"><br>

        <label>Category:</label><br>
        <input type="text" name="category" value="<?php echo htmlspecialchars($product['category']); ?>"><br>

        <label>Price:</label><br>
        <input type="number" step="0.01" name="price" value="<?php echo htmlspecialchars($product['price']); ?>"><br>

        <label>Entity:</label><br>
        <input type="number" name="entity" value="<?php echo htmlspecialchars($product['entity']); ?>"><br>

        <button type="submit" name="update">Update Product</button>
        <a href="dashboard.php">Back to Dashboard</a>
    </form>
    <a href="update_product.php">Back to Products</a>
</div>

</body>
</html>

<?php
$conn->close();
?>
