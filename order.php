<?php
session_start();

$db = new SQLite3('./513.db');

if (!$db) {
    die("Connection failed: " . $db->lastErrorMsg());
}

// 定义 $isLoggedIn 变量
$isLoggedIn = isset($_SESSION['customer_id']); // 假设 'customer_id' 是存储在会话中的登录标识

if (!isset($_SESSION['customer_email']) || $_SESSION['customer_email'] === null) {
    header("Location: login.php");
    exit();
}

if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Luca's Loaves</title>
        <link rel="stylesheet" href="513.css">
        <script>
            window.onload = function() {
                alert("You are not a customer. Please logout and login as a customer.");
                window.location.href = "login.php"; 
            };
        </script>
    </head>
    <body class="order-page">
    <?php
    exit();
}

$result = $db->query("SELECT * FROM customers WHERE email = '" . $_SESSION['customer_email'] . "'");
$customer = $result->fetchArray(SQLITE3_ASSOC);
if (!$customer) {
    session_destroy();
    header("Location: login.php");
    exit();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_order'])) {
    $cart = isset($_POST['cart']) ? json_decode($_POST['cart'], true) : [];

    if (empty($cart)) {
        $_SESSION['error'] = "Cart is empty. Please add items to your cart.";
        header("Location: order.php");
        exit();
    }

    $_SESSION['order_data'] = $cart;

    header("Location: success.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Luca's Loaves</title>
    <link rel="stylesheet" href="513.css">
</head>
<body class="order-page">
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
    <section class="product-table">
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Picture</th>
                    <th>Description</th>
                    <th>Action</th>
                    <th>Quantity</th>
                </tr>
            </thead>
            <tbody>
                <tr data-id="1">
    <td>Sourdough White</td>
    <td>$7.00</td>
    <td><img src="5.jpg" alt="Sourdough White" style="width:200px;height: 200px;"></td>
    <td>Our standard sourdough</td>
    <td><button class="add-to-cart" data-price="7">Add to Cart</button></td>
    <td><input type="number" value="0" class="quantity" data-price="7"></td>
</tr>
<tr data-id="2">
    <td>Sourdough Rye</td>
    <td>$8.00</td>
    <td><img src="6.jpg" alt="Sourdough White" style="width:200px;height: 200px;"></td>
    <td>Sourdough created with 50% rye flour.</td>
    <td><button class="add-to-cart" data-price="8">Add to Cart</button></td>
    <td><input type="number" value="0" class="quantity" data-price="8"></td>
</tr>
<tr data-id="3">
    <td>Sourdough Spelt</td>
    <td>$9.00</td>
    <td><img src="7.jpg" alt="Sourdough White" style="width:200px;height: 200px ;"></td>
    <td>Sourdough created with 100% spelt flour.</td>
    <td><button class="add-to-cart" data-price="9">Add to Cart</button></td>
    <td><input type="number" value="0" class="quantity" data-price="9"></td>
</tr>
<tr data-id="4">
    <td>Sourdough Seeded</td>
    <td>$9.50</td>
    <td><img src="8.jpg" alt="Sourdough White" style="width:200px;height: 200px;"></td>
    <td>Sourdough including a mixture of yummy seeds. </td>
    <td><button class="add-to-cart" data-price="9.5">Add to Cart</button></td>
    <td><input type="number" value="0" class="quantity" data-price="9.5"></td>
</tr>
<tr data-id="6">
    <td>Cappuccino</td>
    <td>$10.00</td>
    <td><img src="9.jpg" alt="Cappuccino" style="width:200px;height: 200px;"></td>
    <td>Freshly Creamy cappuccino.</td>
    <td><button class="add-to-cart" data-price="10">Add to Cart</button></td>
    <td><input type="number" value="0" class="quantity" data-price="10"></td>
</tr>
<tr data-id="7">
    <td>Ice American</td>
    <td>$8.00</td>
    <td><img src="10.jpg" alt="Ice American" style="width:200px;height: 200px;"></td>
    <td>Traditional bitter coffee.</td>
    <td><button class="add-to-cart" data-price="8">Add to Cart</button></td>
    <td><input type="number" value="0" class="quantity" data-price="8"></td>
</tr>
<tr data-id="9">
    <td>Cookie</td>
    <td>$8.00</td>
    <td><img src="11.jpg" alt="Cookie" style="width:200px;height: 200px;"></td>
    <td>Delicious cookies.</td>
    <td><button class="add-to-cart" data-price="8">Add to Cart</button></td>
    <td><input type="number" value="0" class="quantity" data-price="8"></td>
</tr>
<tr data-id="8">
    <td>Pudding</td>
    <td>$5.00</td>
    <td><img src="12.jpg" alt="Pudding" style="width:200px;height: 200px;"></td>
    <td>Sweet pudding.</td>
    <td><button class="add-to-cart" data-price="5">Add to Cart</button></td>
    <td><input type="number" value="0" class="quantity" data-price="5"></td>
</tr>
<tr data-id="10">
    <td>Juice</td>
    <td>$2.00</td>
    <td><img src="13.jpg" alt="Juice" style="width:200px;height: 200px;"></td>
    <td>Fresh fruit juice.</td>
    <td><button class="add-to-cart" data-price="2">Add to Cart</button></td>
    <td><input type="number" value="0" class="quantity" data-price="2"></td>
</tr>
                </tbody>
            </table>
        </section>
        <?php if (isset($_SESSION['customer_username'])): ?>
<?php endif; ?>
<section class="shopping-cart">
        <h2>Shopping Cart</h2>
        <div id="cart-items"></div>
        <div id="cart-totals">
            Total: <span id="total-price">$0.00</span>
        </div>
        <form id="order-form" method="post">
            <input type="hidden" name="cart" id="cart-data">
            <input type="hidden" name="customer_id" value="<?php echo $_SESSION['customer_id']; ?>">
            <input type="hidden" name="submit_order" value="1">
            <button type="submit" id="submit-cart">Submit Cart</button>
        </form>
    </section>
</main>
<footer style="background-color:rgb(0, 72, 255); padding: 10px; text-align: center;">
    <p>© 2024 Luca's Loaves.Collaborators and All Rights Reserved.</p>
    <img src="logo.png" alt="logo" style="height: 50px;width: 50px;">
</footer>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const addToCartButtons = document.querySelectorAll('.add-to-cart');
    const cartItems = document.getElementById('cart-items');
    const totalPrice = document.getElementById('total-price');
    let cart = [];

    addToCartButtons.forEach(button => {
        button.addEventListener('click', function() {
            const price = parseFloat(this.getAttribute('data-price'));
            const row = this.closest('tr');
            const quantityInput = row.querySelector('.quantity');
            const quantity = parseInt(quantityInput.value, 10); 
            const id = row.getAttribute('data-id');
            
            if (quantity > 0 && !isNaN(price) && !isNaN(quantity)) { 
                let itemIndex = cart.findIndex(item => item.id === id);
                if (itemIndex === -1) {
                    cart.push({ price: price, quantity: quantity, id: id });
                } else {
                    cart[itemIndex].quantity += quantity;
                }
                updateCart();
                updateOrderTime(document.getElementById('order-time-' + id));
            }
        });
    });

    function updateCart() {
        let total = 0;
        cartItems.innerHTML = '';
        cart.forEach((item, index) => {
            const itemTotal = (item.price * item.quantity).toFixed(2);
            total += parseFloat(itemTotal);
            cartItems.innerHTML += `<div>${item.quantity}x ${item.price} = $${itemTotal} <button class="remove-from-cart" data-index="${index}">Remove</button></div>`;
        });
        totalPrice.textContent = '$' + total.toFixed(2);
    }

    function updateOrderTime(cell) {
        if (cell) { 
            const now = new Date();
            const formattedTime = now.toLocaleTimeString();
            cell.textContent = formattedTime;
        }
    }

    cartItems.addEventListener('click', function(event) {
        if (event.target.classList.contains('remove-from-cart')) {
            const index = parseInt(event.target.getAttribute('data-index'), 10);
            cart.splice(index, 1);
            updateCart();
        }
    });

    document.getElementById('checkout').addEventListener('click', function() {
        alert(`Total: $${totalPrice.textContent}`);
    });

    document.getElementById('clear-cart').addEventListener('click', function() {
        cart = [];
        updateCart();
    });

    document.getElementById('submit-cart').addEventListener('click', function(event) {
        event.preventDefault(); 
        if (cart.length === 0) {
            alert('Your cart is empty!');
            return;
        }

        let cartData = cart.map((item, index) => {
            return {
                id: item.id,
                price: parseFloat(item.price),
                quantity: parseInt(item.quantity, 10)
            };
        }).filter(item => item !== null); 

        if (cartData.length === 0) {
            alert('Invalid cart items. Please check your cart.');
            return;
        }

        document.getElementById('cart-data').value = JSON.stringify(cartData);

        document.getElementById('order-form').submit();
    });

    const images = document.querySelectorAll('img');
    const imgZoomContainer = document.createElement('div');
    imgZoomContainer.className = 'img-zoom-container';
    const imgZoomOverlay = document.createElement('div');
    imgZoomOverlay.className = 'img-zoom-overlay';
    const zoomImg = document.createElement('img');
    imgZoomOverlay.appendChild(zoomImg);
    imgZoomContainer.appendChild(imgZoomOverlay);
    document.body.appendChild(imgZoomContainer);

    images.forEach(img => {
        img.style.cursor = 'zoom-in';
        img.onclick = function() {
            const imgSrc = this.src;
            zoomImg.src = imgSrc;
            imgZoomOverlay.style.display = 'flex';
        };
    });

    imgZoomOverlay.onclick = function() {
        this.style.display = 'none';
    };
});
</script>
</body>
</html>
<?php
