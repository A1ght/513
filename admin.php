<?php
// admin.php

//Database file path
$dbname = "513.db";

$conn = new SQLite3($dbname);

session_start(); // 确保会话已启动

// 检查用户是否是管理员
if (empty($_SESSION['customer_id']) || empty($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    // 重新生成会话 ID 以提高安全性
    session_regenerate_id(true);

    // 设置一个状态消息，可以在登录页面上显示
    $_SESSION['error_message'] = "You are not an admin. Please logout and login as an admin.";

    // 使用 header() 函数进行重定向
    header("Location: login.php");
    exit();
}

if (!$conn) {
    die('Connection failed: ' . sqlite_error_string($dbname));
}


$customerQuery = "SELECT * FROM customers";
$customerResult = $conn->query($customerQuery);

if (!$customerResult) {
    die('Query failed: ' . $conn->lastErrorMsg());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Luca's Loaves</title>
    <link rel="stylesheet" href="513.css">
</head>
<body>
<header>
    <nav>
        <img src="logo.png" alt="Luca's Loaves Logo" id="logo">
        <span class="nav-title">Members List</span>
        <div class="nav-container">
            <div class="nav-buttons">
                <a href="index.php" class="nav-button">Home</a>
                <a href="about.php" class="nav-button">About us</a>
                <a href="careers.php" class="nav-button">Careers</a>
                <a href="order.php" class="nav-button">Order</a>
                <a href="support.php" class="nav-button">Support</a>
            </div>
            <div class="user-info">
                <?php if (isset($_SESSION['customer_username'])): ?>
                <a href="login.php" class="nav-button login-button">Login</a>
                <?php else: ?>
                <span><?php echo htmlspecialchars($_SESSION['customer_username']); ?> </span>
                    <form action="logout.php" method="post">
                        <button type="submit">Logout</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </nav>
</header>

    <main>
        <h1>Manage</h1>

        <!-- Display all customer registration information -->
        <h2>Members</h2>
        <table class="product-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Manage</th>
                </tr>
            </thead>
            <tbody>
            <?php while($customerRow = $customerResult->fetchArray(SQLITE3_ASSOC)): ?>
            <tr>
                <td><?php echo htmlspecialchars($customerRow['id']) ?></td>
                <td><?php echo htmlspecialchars($customerRow['username']) ?></td>
                <td><?php echo htmlspecialchars($customerRow['phone']) ?></td>
                <td><?php echo htmlspecialchars($customerRow['email']) ?></td>
                <td>
                    <a href="edit_member.php?id=<?php echo $customerRow['id']; ?>">Quit</a>
                    <a href="delete_member.php?id=<?php echo $customerRow['id']; ?>" onclick="return confirm('Are you sure you want to delete this member?');">Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </main>

    <footer style="background-color: #4e4e4c; padding: 10px; text-align: center;">
        <p> 2024 Luca's Loaves.Collaborators and All Rights Reserved.</p>
        <img src="logo.png" alt="logo" style="height: 50px;width: 50px;">
    </footer>
</body>
</html>

<?php
$conn->close();
?>