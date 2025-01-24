<?php
// sales_analysis.php

$dbname = "513.db";

$conn = new SQLite3($dbname);

session_start();

if (!isset($_SESSION['customer_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if (isset($_GET['reset'])) {
    $startDate = '';
    $endDate = '';
    $customerName = '';
    $customerEmail = '';
    $orderID = '';
    $customerID = '';
} else {
    $startDate = isset($_GET['start_date']) ? $_GET['start_date'] : '';
    $endDate = isset($_GET['end_date']) ? $_GET['end_date'] : '';
    $customerName = isset($_GET['customer_name']) ? $_GET['customer_name'] : '';
    $customerEmail = isset($_GET['customer_email']) ? $_GET['customer_email'] : '';
    $orderID = isset($_GET['order_id']) ? $_GET['order_id'] : '';
    $customerID = isset($_GET['customer_id']) ? $_GET['customer_id'] : '';
}

$sql = "SELECT o.order_id, o.order_date, o.total_price AS total_amount, 
               c.id AS customer_id, c.username AS customer_name, c.email AS customer_email, 
               SUM(oi.quantity) AS total_quantity,
               GROUP_CONCAT(p.name || ' x ' || oi.quantity, ', ') AS product_details
        FROM orders o
        JOIN customers c ON o.customer_id = c.id
        JOIN order_items oi ON o.order_id = oi.order_id
        JOIN products p ON oi.product_id = p.id";

$where = [];

if (!empty($startDate)) {
    $where[] = "o.order_date >= '$startDate'";
}
if (!empty($endDate)) {
    $where[] = "o.order_date <= '$endDate'";
}
if (!empty($customerName)) {
    $where[] = "c.username LIKE '%$customerName%'";
}
if (!empty($customerEmail)) {
    $where[] = "c.email LIKE '%$customerEmail%'";
}
if (!empty($orderID)) {
    $where[] = "o.order_id = '$orderID'";
}
if (!empty($customerID)) {
    $where[] = "c.id = '$customerID'";
}

if (!empty($where)) {
    $sql .= " WHERE " . implode(" AND ", $where);
}

$sql .= " GROUP BY o.order_id";

$result = $conn->query($sql);

if (!$result) {
    die('Query failed: ' . $conn->lastErrorMsg());
}

$totalAmount = 0;
$totalSql = "SELECT SUM(o.total_price) AS total_amount FROM orders o";
if (!empty($where)) {
    $totalSql .= " JOIN customers c ON o.customer_id = c.id";
    foreach ($where as $w) {
        $totalSql .= " AND $w";
    }
}
$totalResult = $conn->query($totalSql);
if ($totalResult !== false && $totalRow = $totalResult->fetchArray(SQLITE3_ASSOC)) {
    $totalAmount = $totalRow['total_amount'] ?? 0;
} else {
    echo "<div style='color: red;'>Failed to execute total query: " . $conn->lastErrorMsg() . "</div>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Analysis - Luca's Loaves</title>
    <link rel="stylesheet" href="513.css">
</head>
<body>
<header>
    <nav>
        <img src="logo.png" alt="Luca's Loaves Logo" id="logo">
        <span class="nav-title">Admin</span>
        <div class="nav-container">
            <div class="nav-buttons">
                <a href="index.php" class="nav-button">Home</a>
                <a href="about.php" class="nav-button">About us</a>
                <a href="careers.php" class="nav-button">Careers</a>
                <a href="order.php" class="nav-button">Order</a>
                <a href="support.php" class="nav-button">Support</a>
                <a href="admin.php" class="nav-button">Admin</a>
            </div>
            <div class="user-info">
                <?php if (isset($_SESSION['customer_username'])): ?>
                    <span><?php echo htmlspecialchars($_SESSION['customer_username']); ?> </span>
                    <form action="logout.php" method="post">
                        <button type="submit">Logout</button>
                    </form>
                <?php else: ?>
<a href="login.php" class="nav-button login-button">Login</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>
</header>

<main>
    <h1>Sales Analysis - Luca's Loaves</h1>

    <!-- Search form -->
    <form method="get" action="sales_analysis.php">
    <label for="start_date">Start Date:</label>
    <input type="date" id="start_date" name="start_date" value="<?php echo htmlspecialchars($startDate); ?>">

    <label for="end_date">End Date:</label>
    <input type="date" id="end_date" name="end_date" value="<?php echo htmlspecialchars($endDate); ?>">

    <label for="customer_name">Customer Name:</label>
    <input type="text" id="customer_name" name="customer_name" placeholder="Search by username" value="<?php echo htmlspecialchars($customerName); ?>">

    <label for="customer_email">Customer Email:</label>
    <input type="email" id="customer_email" name="customer_email" placeholder="Search by email" value="<?php echo htmlspecialchars($customerEmail); ?>">

    <label for="order_id">Order ID:</label>
    <input type="text" id="order_id" name="order_id" placeholder="Search by order ID" value="<?php echo htmlspecialchars($orderID); ?>">

    <label for="customer_id">Customer ID:</label>
    <input type="text" id="customer_id" name="customer_id" placeholder="Search by customer ID" value="<?php echo htmlspecialchars($customerID); ?>">

    <input type="submit" value="Search">
    <input type="submit" value="Reset" name="reset">
</form>


    <!-- Order information table -->
    <table class="product-table">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Customer ID</th>
                <th>Order Date</th>
                <th>Customer Name</th>
                <th>Customer Email</th>
                <th>Products</th>
                <th>Total Quantity</th>
                <th>Total Amount</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $result->fetchArray(SQLITE3_ASSOC)): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['order_id']) ?></td>
                <td><?php echo htmlspecialchars($row['customer_id']) ?></td>
                <td><?php echo htmlspecialchars($row['order_date']) ?></td>
                <td><?php echo htmlspecialchars($row['customer_name']) ?></td>
                <td><?php echo htmlspecialchars($row['customer_email']) ?></td>
                <td><?php echo htmlspecialchars($row['product_details']) ?></td>
                <td><?php echo htmlspecialchars($row['total_quantity']) ?></td>
                <td><?php echo number_format($row['total_amount'], 2); ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="7">Total Amount</th>
                <td><?php echo number_format($totalAmount, 2); ?></td>
            </tr>
        </tfoot>
    </table>
    <a href="admin.php" class=" view-button">View Member List</a>
    
</main>

<footer style="background-color: #71221E; padding: 10px; text-align: center;">
    <p>© 2024 Luca's Loaves. All rights reserved.</p>
</footer>
</body>
</html>
