<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loading...</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: #000;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            overflow: hidden;
        }

        .splash-container {
            position: relative;
            width: 100vw;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .ball {
            width: 50px;
            height: 50px;
            background-color: #ff4500;
            border-radius: 50%;
            position: absolute;
            animation: bounce 2s infinite ease-in-out;
        }

        @keyframes bounce {
            0% { top: 10%; }
            50% { top: 80%; }
            100% { top: 10%; }
        }

        .text {
            position: absolute;
            bottom: 15%;
            color: white;
            font-size: 20px;
            font-family: Arial, sans-serif;
        }
    </style>
</head>
<body>

<div class="splash-container">
    <div class="ball"></div>
    <div class="text">Loading...</div>
</div>

<script>
    setTimeout(() => {
        window.location.href = "dashboard.php"; // Redirect to dashboard after 3 seconds
    }, 3000);
</script>

</body>
</html>
