<?php
// careers.php

session_start();

$dbname = "513.db";
$successMessage = '';
$errorMessage = '';

$db = new SQLite3($dbname);

// 设置锁定超时时间（例如，10秒）
$db->exec("PRAGMA busy_timeout=10000;");

if (!$db) {
    die("Connection failed: " . sqlite3_last_error($db));
}

$isLoggedIn = isset($_SESSION['customer_id']);
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $number = $_POST['number'];
    $email = $_POST['email'];
    $position = $_POST['position'];
    $gender = $_POST['gender'];
    $file1 = isset($_FILES["file1"]["name"]) ? $_FILES["file1"]["name"] : 'No file';
    $file2 = isset($_FILES["file2"]["name"]) ? $_FILES["file2"]["name"] : 'No file';

    // 检查文件上传是否成功
    if ($_FILES["file1"]["error"] !== UPLOAD_ERR_OK || $_FILES["file2"]["error"] !== UPLOAD_ERR_OK) {
        $errorMessage = "File upload failed: " . $_FILES["file1"]["error"] . " " . $_FILES["file2"]["error"];
    } else {
        // 开始事务
        $db->exec("BEGIN TRANSACTION;");

        // 准备 SQL 语句
        $sql = "INSERT INTO career_applications (name, number, email, position, gender, file1, file2) VALUES (:name, :number, :email, :position, :gender, :file1, :file2)";
        $stmt = $db->prepare($sql);

        // 检查 prepare 方法是否成功
        if ($stmt === false) {
            $errorMessage = "Error preparing statement: " . $db->lastErrorMsg();
            $db->exec("ROLLBACK;");
        } else {
            // 绑定参数
            $stmt->bindValue(':name', $name, SQLITE3_TEXT);
            $stmt->bindValue(':number', $number, SQLITE3_TEXT);
            $stmt->bindValue(':email', $email, SQLITE3_TEXT);
            $stmt->bindValue(':position', $position, SQLITE3_TEXT);
            $stmt->bindValue(':gender', $gender, SQLITE3_TEXT);
            $stmt->bindValue(':file1', $file1, SQLITE3_TEXT);
            $stmt->bindValue(':file2', $file2, SQLITE3_TEXT);

            // 执行 SQL 语句
            if ($stmt->execute()) {
                $db->exec("COMMIT;");
                $successMessage = "Application submitted successfully!\n";
                $successMessage .= "User Name: $name\n";
                $successMessage .= "Phone Number: $number\n";
                $successMessage .= "Email: $email\n";
                $successMessage .= "Position Applied: $position\n";
                $successMessage .= "Uploaded Files:\n";
                if ($file1 != 'No file') {
                    $successMessage .= "- $file1\n";
                }
                if ($file2 != 'No file') {
                    $successMessage .= "- $file2\n";
                }
            } else {
                $db->exec("ROLLBACK;");
                $errorMessage = "Error: " . $db->lastErrorMsg();
            }
        }
    }
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
<footer style="background-color: #4e4e4c; padding: 10px; text-align: center;">
    <p> 2024 Luca's Loaves.Collaborators and All Rights Reserved.</p>
</footer>
</body>
</html>