<?php
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    // Check if email already exists
    $check_email = $conn->query("SELECT * FROM users WHERE email='$email'");

    if ($check_email->num_rows > 0) {
        echo "<script>alert('Email already exists!');</script>";
    } else {
        $sql = "INSERT INTO users (fname, lname, email, role, password) VALUES ('$fname', '$lname', '$email', '$role', '$password')";
        
        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Registration Successful!'); window.location='login.php';</script>";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="style.css"> <!-- Linking the CSS file -->
</head>
<body>

<div class="container">
    <h2>Register</h2>
    <form method="POST">
        <input type="text" name="fname" placeholder="Enter Your First Name" required>
        <input type="text" name="lname" placeholder="Enter Your Last Name" required>
        <input type="email" name="email" placeholder="Email" required>
        <select name="role">
            <option value="User">User</option>
            <option value="Admin">Admin</option>
        </select>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Register</button>
    </form>
</div>

</body>
</html>
