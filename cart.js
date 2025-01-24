document.addEventListener('DOMContentLoaded', function() {
    const addToCartButtons = document.querySelectorAll('.add-to-cart');
    const cartItems = document.getElementById('cart-items');
    const totalPrice = document.getElementById('total-price');

    let cart = [];

    addToCartButtons.forEach(button => {
        button.addEventListener('click', function() {
            const price = this.getAttribute('data-price');
            const quantityInput = this.nextElementSibling;
            const quantity = parseInt(quantityInput.value);
            if (quantity > 0) {
                cart.push({ price: price, quantity: quantity });
                updateCart();
            }
        });
    });

    function updateCart() {
        totalPrice.textContent = '$0.00';
        cartItems.innerHTML = '';
        cart.forEach(item => {
            const itemTotal = (item.price * item.quantity).toFixed(2);
            totalPrice.textContent = (parseFloat(totalPrice.textContent) + itemTotal).toFixed(2);
            cartItems.innerHTML += `<div>${item.quantity}x ${item.price} = ${itemTotal}</div>`;
        });
    }

    checkoutButton.addEventListener('click', function() {
        alert(`Total: $${totalPrice.textContent}`);
    });

    clearCartButton.addEventListener('click', function() {
        cart = [];
        updateCart();
    });
});
function updateOrderTime() {
    const orderTimeCell = document.getElementById('order-time');
    const now = new Date();
    const formattedTime = now.toLocaleTimeString(); 
    orderTimeCell.textContent = formattedTime; 
}

addToCartButtons.forEach(button => {
    button.addEventListener('click', function() {
        const price = this.getAttribute('data-price');
        const quantityInput = this.nextElementSibling;
        const quantity = parseInt(quantityInput.value);
        if (quantity > 0) {
            cart.push({ price: price, quantity: quantity });
            updateCart();
            updateOrderTime(); 
        }
    });
});