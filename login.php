<?php
include 'db_connect.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    // Secure prepared statement to fetch user data
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        // Verify the password
        if (password_verify($password, $row['password'])) {
            // Set session variables
            $_SESSION['user'] = $row['fname'];  // Store user's first name (or any other relevant info)
            $_SESSION['user_id'] = $row['id'];  // Store user's ID
            $_SESSION['role'] = $row['role'];   // Store user's role (Admin/User)

            // Redirect to dashboard after successful login
            echo "<script>alert('Login Successful!'); window.location='dashboard.php';</script>";
            exit();
        } else {
            echo "<script>alert('Incorrect password!');</script>";
        }
    } else {
        echo "<script>alert('Email not found!');</script>";
    }

    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="style2.css">
</head>
<body>

<div class="login-container">
    <h2>Login</h2>
    <form method="POST">
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
    </form>
    <p>Don't have an account? <a href="register.php">Register</a></p>
</div>

</body>
</html>
