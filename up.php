<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Product</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f5f5f5;
        }
        .container {
            width: 400px;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            font-weight: bold;
            margin-bottom: 10px;
        }
        .image-placeholder {
            width: 80px;
            height: 80px;
            background: #ccc;
            display: flex;
            justify-content: center;
            align-items: center;
            border-radius: 4px;
            margin-bottom: 10px;
        }
        input, textarea, button {
            width: 100%;
            margin-bottom: 10px;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            background: black;
            color: white;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">Inventory : Product</div>
        <div class="image-placeholder">📷</div>
        <input type="text" placeholder="Product Name">
        <textarea placeholder="Product Description"></textarea>
        <input type="number" placeholder="Price">
        <input type="number" placeholder="Stock">
        <button>Update Product</button>
    </div>
</body>
</html>
