<?php
session_start();

// 定义 $isLoggedIn 变量
$isLoggedIn = isset($_SESSION['customer_id']); // 假设 'customer_id' 是存储在会话中的登录标识

$db = new SQLite3('513.db');

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $email = trim($_POST['email']);
    $role = trim($_POST['role']); 

    $stmt = $db->prepare("SELECT * FROM customers WHERE username = :username AND email = :email");
    $stmt->bindValue(":username", $username, SQLITE3_TEXT);
    $stmt->bindValue(":email", $email, SQLITE3_TEXT);
    $result = $stmt->execute();
    $customer = $result->fetchArray(SQLITE3_ASSOC);

    if ($customer) {
        if (password_verify($password, $customer['password'])) {
            $_SESSION['customer_username'] = $customer['username'];
            $_SESSION['customer_email'] = $customer['email'];
            $_SESSION['customer_id'] = $customer['id']; 
            $_SESSION['role'] = $customer['role'] ?? 'customer'; // 默认角色为 'customer'

            if ($customer['role'] === 'admin') {
                header("Location: admin.php");
                exit();
            } else {
                header("Location: order.php");
                exit();
            }
        } else {
            $error = "invalid_password";
        }   
    } else {
        $error = "account_not_found";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="513.css">
    <script>
        window.onload = function() {
            var error = "<?php echo $error; ?>";
            function handleError(message, url) {
                alert(message);
                if (url) {
                    window.location.href = url;
                }
            }
            if (error === "account_not_found") {
                handleError("Account not found. Please register first.", "register.php");
            } else if (error === "invalid_password") {
                handleError("Invalid password. Please try again.");
            }
        };
    </script>
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
    <section class="form-container">
        <h1>Login</h1>
        <div id="error-message" style="color: red; margin-bottom: 10px;">
            <?php if (!empty($error)): ?>
                <?php echo htmlspecialchars($error); ?>
            <?php endif; ?>
        </div>
        <form method="POST" action="login.php" class="login-form">
            <div>
                <label for="username">Username:</label>
                <input type="text" name="username" id="username" required>
            </div>
            <div>
                <label for="password">Password:</label>
                <input type="password" name="password" id="password" required>
            </div>
            <div>
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" required>
            </div>
            <div>
                <label for="role">Identity:</label>
                <select name="role" id="role">
                    <option value="customer">Customer</option>
                    <option value="admin">Administrators</option>
                </select>
            </div>
            <div class="button-container">
                <button type="submit">Login</button>
            </div>
        </form>
        <div class="register-link">
            <a href="register.php">Don't have an account? Register here</a>
        </div>
    </section>
</main>

<footer style="background-color:rgb(0, 72, 255); padding: 10px; text-align: center;">
    <p> 2024 Luca's Loaves.Collaborators and All Rights Reserved.</p>
    <img src="logo.png" alt="logo" style="height: 50px;width: 50px;">
</footer>
</body>
</html>