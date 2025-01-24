<?php
session_start();

$isLoggedIn = isset($_SESSION['customer_username']);
$customerUsername = $isLoggedIn ? htmlspecialchars($_SESSION['customer_username']) : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Luca's Loaves</title>
    <link rel="stylesheet" href="513.css">
</head>
<body>
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
        <section class="aboutus">
            <div class="logo">
                <img src="picture/logo.jpg" alt="Logo">
            </div>
            <div class="main"><h1>We have a very strong team of chefs to ensure that<br> our customers can enjoy the best dining experience</h1></div>
            <div class="container">
                <div class="photo"><img src="2.png" alt="Photo 1"></div>
                <div class="text">
                    <h1>Luca, the founder of Luca's Loaves, started his career as a lifeguard but was laid off after a few years. Instead of letting this setback define him, Luca turned his passion for bread-making into a thriving business. He believes that the art of crafting sourdough bread is not just about the ingredients but also about the dedication and care that goes into each loaf.</h1>
                </div>
            </div>
        
            <div class="container">
                <div class="photo1"><img src="3.jpg" alt="Photo 2"></div>
                <div class="text">
                    <h1>Our team is made up of dedicated individuals who share Luca's passion for bread and coffee. Each member brings their unique skills and experiences to create a warm and welcoming atmosphere for our customers.</h1>
                </div>
            </div>
        
            <div class="container">
                <div class="photo2"><img src="4.jpg" alt="Photo 3"></div>
                <div class="text">
                    <h1>We are located at Mount Lindesay HWY in Australia and can be reached at  02 9876 5432. You can also email us at info@lucasloaves.com.au. We look forward to serving you!</h1>
                </div>
            </div>
        
            <!-- Australia Map -->
            <div class="map-container">
                <h2 style="color:rgb(0, 0, 0)">Our Location<br>
                    Welcome everyone to visit
                </h2>
                <iframe src="map.png" width="600" height="600"></iframe>
            </div>
        </section>
    </main>
    <footer style="background-color:rgb(0, 0, 255); padding: 10px; text-align: center;">
        <p> 2024 Luca's Loaves.Collaborators and All Rights Reserved.</p>
    </footer>
</body>
</html>