<?php
// edit_member.php

$dbname = "513.db";

$conn = new SQLite3($dbname);

if (!$conn) {
    die('无法连接到数据库: ' . $conn->lastErrorMsg());
}

$customerId = isset($_GET['id']) ? intval($_GET['id']) : 0;

$customerQuery = "SELECT * FROM customers WHERE id = $customerId";
$customerResult = $conn->query($customerQuery);

if ($customerResult) {
    $customerInfo = $customerResult->fetchArray(SQLITE3_ASSOC);
} else {
    die('无法检索客户信息。');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $email = $_POST['email'] ?? '';
    $address = $_POST['address'] ?? '';
    $city = $_POST['city'] ?? '';
    $dateOfBirth = $_POST['date_of_birth'] ?? '';
    $producePreferences = $_POST['produce_preferences'] ?? '';

    $stmt = $conn->prepare("UPDATE customers SET username = :username, password = :password, phone = :phone, email = :email, address = :address, city = :city, date_of_birth = :dateOfBirth, produce_preferences = :producePreferences WHERE id = :customerId");
    $stmt->bindValue(':username', $username);
    $stmt->bindValue(':password', password_hash($password, PASSWORD_DEFAULT)); 
    $stmt->bindValue(':phone', $phone);
    $stmt->bindValue(':email', $email);
    $stmt->bindValue(':address', $address);
    $stmt->bindValue(':city', $city);
    $stmt->bindValue(':dateOfBirth', $dateOfBirth);
    $stmt->bindValue(':producePreferences', $producePreferences);
    $stmt->bindValue(':customerId', $customerId);

    if ($stmt->execute()) {
        header("Location: admin.php"); 
        exit();
    } else {
        echo "<p>更新成员信息时出错: " . $stmt->lastErrorMsg() . "</p>";
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Member</title>
    <link rel="stylesheet" href="513.css">
</head>
<body>
    <header>
        <nav>
            <img src="logo.png" alt="Luca's Loaves Logo" id="logo">
            <div class="nav-buttons">
                <a href="index.html" class="nav-button">Home</a>
                <a href="about.html" class="nav-button">About us</a>
                <a href="careers.php" class="nav-button">Careers</a>
                <a href="order.php" class="nav-button">Order</a>
                <a href="support.html" class="nav-button">Support</a>
                <a href="admin.php" class="nav-button">Admin</a>
            </div>
        </nav>
    </header>

    <main>
        <h1>Edit Member</h1>
        <form method="post">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($customerInfo['username']); ?>">

            <label for="password">Password:</label>
            <input type="password" id="password" name="password">

            <label for="phone">Phone:</label>
            <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($customerInfo['phone']); ?>">

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($customerInfo['email']); ?>">

            <label for="address">Address:</label>
            <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($customerInfo['address']); ?>">

            <label for="city">City:</label>
            <input type="text" id="city" name="city" value="<?php echo htmlspecialchars($customerInfo['city']); ?>">

            <label for="date_of_birth">Date of Birth:</label>
            <input type="date" id="date_of_birth" name="date_of_birth" value="<?php echo htmlspecialchars($customerInfo['date_of_birth']); ?>">

            <label for="produce_preferences">Product Preferences:</label>
            <textarea id="produce_preferences" name="produce_preferences"><?php echo htmlspecialchars($customerInfo['produce_preferences']); ?></textarea>

            <input type="submit" value="Update Member">
        </form>
    </main>

    <footer>
        <p>© 2024 Luca's Loaves. All rights reserved.</p>
    </footer>
</body>
</html>