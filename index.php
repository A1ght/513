<?php
session_start();

$isLoggedIn = isset($_SESSION['customer_id']);
$isCustomer = $isLoggedIn && isset($_SESSION['role']) && $_SESSION['role'] === 'customer';
$isAdmin = $isLoggedIn && isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Luca's Loaves</title>
    <link rel="stylesheet" href="513.css">
    <script>
    </script>
</head>
<body class="index-body">
<header>
    <nav>
        <img src="logo.png" alt="Luca's Loaves Logo" id="logo">
        <span class="nav-title">Welcome to Luca's Loaves！</span>
        <div class="nav-container">
            <div class="nav-buttons">
                <a href="index.php" class="nav-button">Home</a>
                <a href="about.php" class="nav-button">About us</a>
                <a href="careers.php" class="nav-button">Careers</a>
                <a href="order.php" class="nav-button" onclick="return checkRoleAndGoOrder()">Order</a>
                <a href="support.php" class="nav-button">Support</a>
            </div>
            <div class="user-info">
                <?php if ($isLoggedIn): ?>
                    <span><?php echo htmlspecialchars($_SESSION['customer_username']); ?> </span>
                <?php else: ?>
                    <a href="login.php" class="nav-button">Login</a>
                <?php endif; ?>
                <form action="logout.php" method="post">
                    <button type="submit">Logout</button>
                </form>
            </div>
        </div>
    </nav>
</header>
<main>
    <section class="hero">
        <img src="backgourd.jpg" alt="Luca's Loaves Cafe" class="background-image">
        <div class="hero-text">
            <img src="1.jpg" alt=  class="hero-logo" style="height: 200px;width: 100px;;">
            <h1>If you want to experience the charm of food, take a look!</h1>
            <a href="order.php" class="go-button" onclick="return checkRoleAndGoOrder()">Ok,thanks</a> 
        </div>
    </section>
</main>
</body>
</html>