<?php
session_start();

if (!isset($_SESSION['customer_id']) || $_SESSION['customer_id'] === null) {
    echo "<p>Error: No customer ID found. Please log in first.</p>";
    exit();
}

if (isset($_SESSION['customer_id']) && isset($_SESSION['order_data'])) {
    $customer_id = $_SESSION['customer_id'];
    $cart = $_SESSION['order_data'];
    unset($_SESSION['order_data']); 

    echo "<!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Order Confirmation</title>
        <link rel='stylesheet' href='513.css'>
    </head>
    <body>
    <header>
        <nav>
            <img src='logo.png' alt='Luca's Loaves Logo' id='logo'>
            <span class='nav-title'>Order Confirmation</span>
            <div class='nav-buttons'>
                <a href='index.php' class='nav-button'>Home</a>
                <a href='about.php' class='nav-button'>About us</a>
                <a href='careers.php' class='nav-button'>Careers</a>
                <a href='order.php' class='nav-button'>Order</a>
                <a href='support.php' class='nav-button'>Support</a>
            </div>
            <div class='user-info'>
                <span>" . htmlspecialchars($_SESSION['customer_username']) . "</span>
                <form action='logout.php' method='post'>
                    <button type='submit'>Logout</button>
                </form>
            </div>
        </nav>
    </header>
    <main>
        <section class='order-confirmation'>
            <h1>Order Confirmation</h1>
            <table border='1' style='width:100%;'>
                <tr style='background-color:#4CAF50;'>
                    <th>Product</th><th>Price</th><th>Quantity</th><th>Subtotal</th>
                </tr>
                <tbody>
            ";

    $total = 0;
    foreach ($cart as $item) {
        $subtotal = $item['price'] * $item['quantity'];
        $total += $subtotal;
        echo "<tr data-id='" . htmlspecialchars($item['id']) . "'>
                <td>" . htmlspecialchars($item['name']) . "</td>
                <td>$" . number_format($item['price'], 2) . "</td>
                <td><input type='number' class='quantity-input' value='" . htmlspecialchars($item['quantity']) . "' min='0'></td>
                <td class='subtotal'>$" . number_format($subtotal, 2) . "</td>
            </tr>";
    }
    echo "<tr style='background-color:#4CAF50;'>
                <td colspan='3'>Grand Total:</td><td id='grand-total'>$" . number_format($total, 2) . "</td>
            </tr>
            </tbody>
        </table>";

    echo "<button id='submit-order-button'>Insert into Database</button>";
    echo "<button onclick='window.location.href=\"order.php\"'>Return to Order Page</button>";

    echo "<script>
    document.addEventListener('DOMContentLoaded', function() {
        function updateSubtotalAndTotal() {
            var newTotal = 0;
            document.querySelectorAll('.quantity-input').forEach(function(input) {
                var tr = input.closest('tr');
                var pricePerItem = parseFloat(tr.cells[1].textContent.replace('$', ''));
                var quantity = parseInt(input.value, 10);
                var subtotal = pricePerItem * quantity;
                tr.cells[3].textContent = '$' + subtotal.toFixed(2);
                newTotal += subtotal;
            });
            document.getElementById('grand-total').textContent = '$' + newTotal.toFixed(2);
        }

        document.querySelectorAll('.quantity-input').forEach(function(input) {
            input.addEventListener('input', updateSubtotalAndTotal);
        });

        document.getElementById('submit-order-button').addEventListener('click', function() {
            var orderData = [];
            document.querySelectorAll('.quantity-input').forEach(function(input) {
                var tr = input.closest('tr');
                var id = tr.getAttribute('data-id');
                var price = parseFloat(tr.cells[1].textContent.replace('$', ''));
                var quantity = parseInt(input.value, 10);
                orderData.push({id: id, price: price, quantity: quantity});
            });
            fetch('submit_to_database.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ orderData: orderData, customer_id: '" . $customer_id . "' })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    var username = '" . htmlspecialchars($_SESSION['customer_username']) . "';
                    alert('Order Successful Mr. ' + username); 
                    window.location.href = 'order.php';
                } else {
                    alert(data.message); 
                }
            })
            .catch((error) => {
                console.error('Error:', error);
                alert('An error occurred while submitting the order.'); 
            });
        });
    });
    </script>";
    echo "</section>
    </main>
    <footer>
        <p>© 2024 Luca's Loaves. All rights reserved.</p>
    </footer>
    </body>
    </html>";
} else {
    echo "<p>Error: No cart data provided. Please go back and try again.</p>";
}
?>