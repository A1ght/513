<?php
session_start();

$db = new SQLite3('./513.db');

if (!$db) {
    die("Connection failed: " . $db->lastErrorMsg());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['orderData']) && isset($_POST['customer_id'])) {
    $orderData = json_decode($_POST['orderData'], true);
    $customer_id = $_POST['customer_id'];

    // 开始事务
    $db->exec("BEGIN TRANSACTION;");

    // 插入订单记录
    $orderSql = "INSERT INTO orders (customer_id, total_amount) VALUES (:customer_id, :total_amount)";
    $orderStmt = $db->prepare($orderSql);
    $totalAmount = 0;

    foreach ($orderData as $item) {
        $totalAmount += $item['price'] * $item['quantity'];
    }

    $orderStmt->bindValue(':customer_id', $customer_id, SQLITE3_INTEGER);
    $orderStmt->bindValue(':total_amount', $totalAmount, SQLITE3_FLOAT);
    $orderStmt->execute();

    $order_id = $db->lastInsertRowID();

    // 插入订单详情记录
    $orderDetailSql = "INSERT INTO order_details (order_id, product_id, quantity, unit_price) VALUES (:order_id, :product_id, :quantity, :unit_price)";
    $orderDetailStmt = $db->prepare($orderDetailSql);

    foreach ($orderData as $item) {
        $orderDetailStmt->bindValue(':order_id', $order_id, SQLITE3_INTEGER);
        $orderDetailStmt->bindValue(':product_id', $item['id'], SQLITE3_INTEGER);
        $orderDetailStmt->bindValue(':quantity', $item['quantity'], SQLITE3_INTEGER);
        $orderDetailStmt->bindValue(':unit_price', $item['price'], SQLITE3_FLOAT);
        $orderDetailStmt->execute();
    }

    // 提交事务
    $db->exec("COMMIT;");

    echo json_encode(['status' => 'success', 'message' => 'Order submitted successfully!']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
}

$db->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Luca's Loaves Careers</title>
    <link rel="stylesheet" href="513.css">
    <style>
        .hero-careers {
            background-image: url('background.jpg'); /* 修改背景图片 */
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            height: 100vh; /* 覆盖整个视口高度 */
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            text-align: center;
        }
    </style>
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
    <section class="hero-careers">
        <div class="form-container">
            <?php if (!empty($successMessage)): ?>
                <div class="message success">
                    <pre><?php echo $successMessage; ?></pre>
                </div>
            <?php endif; ?>
            <?php if (!empty($errorMessage)): ?>
                <div class="message error">
                    <pre><?php echo $errorMessage; ?></pre>
                </div>
            <?php endif; ?>
            <form method="post" action="careers.php" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="name">Name:</label>
                    <input type="text" id="name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="number">Number:</label>
                    <input type="text" id="number" name="number" required>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="position">Position:</label>
                    <select id="position" name="position" required>
                        <option value="Barista">Barista</option>
                        <option value="Baker">Baker</option>
                        <option value="Waiter">Waiter</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="gender">Gender:</label>
                    <select id="gender" name="gender" required>
                        <option value="Female">Female</option>
                        <option value="Male">Male</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="file1">File 1 (PDF):</label>
                    <input type="file" id="file1" name="file1" accept=".pdf" required>
                </div>
                <div class="form-group">
                    <label for="file2">File 2 (PDF):</label>
                    <input type="file" id="file2" name="file2" accept=".pdf" required>
                </div>
                <button type="submit">Submit Application</button>
            </form>
        </div>
    </section>
</main>
<footer style="background-color:rgb(0, 85, 255); padding: 10px; text-align: center;">
    <p> 2024 Luca's Loaves.Collaborators and All Rights Reserved.</p>
</footer>
</body>
</html>