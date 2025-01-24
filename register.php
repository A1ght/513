<?php
session_start();

$db = new SQLite3('513.db');

$error = "";
$registrationSuccess = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $role = trim($_POST['role']); 

    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    $result = $db->query("SELECT * FROM customers WHERE email = '$email'");
    $customer = $result->fetchArray(SQLITE3_ASSOC);

    if ($customer) {
        $error = "Email already registered. Please login.";
    } else {
        $stmt = $db->prepare("INSERT INTO customers (username, password, phone, email) VALUES (:username, :passwordHash, :phone, :email)");
        $stmt->bindValue(':username', $username, SQLITE3_TEXT);
        $stmt->bindValue(':passwordHash', $passwordHash, SQLITE3_TEXT);
        $stmt->bindValue(':phone', $phone, SQLITE3_TEXT);
        $stmt->bindValue(':email', $email, SQLITE3_TEXT);

        if ($stmt->execute()) {
            $registrationSuccess = true;
        } else {
            $error = "Error inserting data into the database: " . $stmt->lastErrorMsg();
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
    <title>Register</title>
    <link rel="stylesheet" href="513.css">
    <script>
        function validateEmail(email) {
            var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailPattern.test(email);
        }

        function validateUsername(username) {
            return username.length <= 8 && !/^\d+$/.test(username);
        }

        function validateForm() {
            var username = document.getElementById('username').value;
            var email = document.getElementById('email').value;

            if (!validateEmail(email)) {
                alert("Please enter a valid email address.");
                return false;
            }

            if (!validateUsername(username)) {
                alert("Username must be 8 characters or less and cannot be all digits.");
                return false;
            }

            return true;
        }

        window.onload = function() {
            var registrationSuccess = "<?php echo $registrationSuccess ? 'true' : 'false'; ?>";
            if (registrationSuccess === "true") {
                alert("Registration successful. Please login.");
                window.location.href = "login.php";
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
            </div>    
        </nav>
    </header>

    <main>
        <section class="form-container">
            <h1>Register</h1>
            <form method="POST" action="register.php" onsubmit="return validateForm()">
                <div>
                    <label for="username">Username:</label>
                    <input type="text" name="username" id="username" required maxlength="8">
                </div>
                <div>
                    <label for="password">Password:</label>
                    <input type="password" name="password" id="password" required>
                </div>
                <div>
                    <label for="phone">Phone:</label>
                    <input type="text" name="phone" id="phone" required>
                </div>
                <div>
                    <label for="email">Email:</label>
                    <input type="email" name="email" id="email" required>
                </div>
                <div>
                    <label for="role">Role:</label>
                    <select name="role" id="role">
                        <option value="customer">Customer</option>
                        <option value="admin">Administrators</option>
                    </select>
                </div>
                <button type="submit">Register</button>
            </form>
            <p class="error-message"><?php echo htmlspecialchars($error); ?></p>
        </section>
    </main>

    <footer style="background-color:rgb(25, 0, 255); padding: 10px; text-align: center;">
        <p> 2024 Luca's Loaves.Collaborators and All Rights Reserved.</p>
        <img src="logo.png" alt="logo" style="height: 50px;width: 50px;">
    </footer>
</body>
</html>