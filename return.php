<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Database connection
    $host = 'localhost';
    $db = 'your_database';
    $user = 'your_user';
    $pass = 'your_password';
    $dsn = "mysql:host=$host;dbname=$db";

    try {
        $pdo = new PDO($dsn, $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die("Could not connect to the database: " . $e->getMessage());
    }

    // Get product ID from POST request
    $productId = $_POST['productId'];

    // Update product status to 'returned'
    $sql = "UPDATE products SET status = 'returned' WHERE id = :productId";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':productId' => $productId]);

    echo "Product returned successfully!";
}
?>
