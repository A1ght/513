<?php
// support.php

session_start();

// 定义 $isLoggedIn 变量
$isLoggedIn = isset($_SESSION['customer_id']); // 假设 'customer_id' 是存储在会话中的登录标识


$dbname = "513.db"; // 添加了等号两边的空格

$_SESSION['successMessage'] = '';

$conn = new SQLite3($dbname);

if (!$conn) {
    die("Connection failed: " . $conn->lastErrorMsg()); // 修正了函数调用
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $info = $_POST['info'];

    $sql = "INSERT INTO support_messages (name, email, info) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(1, $name, SQLITE3_TEXT);
    $stmt->bindValue(2, $email, SQLITE3_TEXT);
    $stmt->bindValue(3, $info, SQLITE3_TEXT);
    if ($stmt->execute()) {
        $_SESSION['successMessage'] = "Thank you, $name, for your message! We will contact you at $email with more information.";
    } else {
        $_SESSION['errorMessage'] = "Error: " . $conn->lastErrorMsg();
    }
}

$conn = new SQLite3($dbname);

if (!$conn) {
    die("Connection failed: " . sqlite3_last_error($conn));
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Talk to us</title>
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
        <section class="support-dialogue">
            <h2>Provide feedback on your suggestions</h2>
            <?php if (isset($_SESSION['successMessage'])): ?>
                <div class="alert alert-success" role="alert">
                    <?php echo $_SESSION['successMessage']; unset($_SESSION['successMessage']); ?>
                </div>
            <?php elseif (isset($_SESSION['errorMessage'])): ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo $_SESSION['errorMessage']; unset($_SESSION['errorMessage']); ?>
                </div>
            <?php endif; ?>
            <form method="post" action="support.php">
                <div class="form-group">
                    <label for="name">User name</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="email">Your Email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="info">Your suggestions</label>
                    <textarea class="form-control" id="info" name="info" rows="3" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        </section>
    </main>
    <footer style="background-color:rgb(21, 0, 255); padding: 10px; text-align: center;">
        <p> 2024 Luca's Loaves.Collaborators and All Rights Reserved.</p>
        <img src="logo.png" alt="logo" style="height: 50px;width: 50px;">
    </footer>
</body>
</html>