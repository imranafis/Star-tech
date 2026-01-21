<div class="checkout-container">
    <h2>Checkout</h2>
    
    <div id="cart-items">
        <!-- Cart items will be loaded here -->
    </div>
    

    
    <div class="shipping-form">
        <h3>Shipping Information</h3>
        <div class="form-group">
            <label for="customerName">Full Name *</label>
            <input type="text" id="customerName" class="form-control" placeholder="Enter your full name" required>
        </div>
        <div class="form-group">
            <label for="email">Email Address *</label>
            <input type="email" id="email" class="form-control" placeholder="Enter your email" required>
        </div>
        <div class="form-group">
            <label for="phone">Phone Number *</label>
            <input type="tel" id="phone" class="form-control" placeholder="Enter your phone number" required>
        </div>
        <div class="form-group">
            <label for="address">Shipping Address *</label>
            <textarea id="address" class="form-control" rows="3" placeholder="Enter complete shipping address" required></textarea>
        </div>
        <div class="form-group">
            <label for="paymentMethod">Payment Method *</label>
            <select id="paymentMethod" class="form-control">
                <option value="cod">Cash on Delivery</option>
                <option value="card">Credit/Debit Card</option>
            </select>
        </div>
    </div>
    
    <button onclick="processCheckout()" class="btn btn-success btn-lg btn-block">
        Complete Purchase
    </button>
</div>

<script>
    /*
// Load cart items on page load
document.addEventListener('DOMContentLoaded', function() {
    loadCartItems();
});

function loadCartItems() {
    fetch('/api/get-cart.php')
    .then(response => response.json())
    .then(data => {
        if (data.success && data.cart && data.cart.items.length > 0) {
            displayCartItems(data.cart);
        } else {
            document.getElementById('cart-items').innerHTML = 
                '<div class="alert alert-warning">Your cart is empty. Please add items to checkout.</div>';
            document.getElementById('cart-total').textContent = '0.00';
            disableCheckout();
        }
    })
    .catch(error => {
        console.error('Error loading cart:', error);
        document.getElementById('cart-items').innerHTML = 
            '<div class="alert alert-danger">Error loading cart items.</div>';
    });
}

function displayCartItems(cart) {
    let html = '<div class="cart-items-list">';
    
    cart.items.forEach((item, index) => {
        if (item.type === 'build_pc') {
            html += `<div class="cart-item build-item">
                <h4>${item.name}</h4>
                <ul>`;
            
            Object.values(item.components).forEach(comp => {
                html += `<li>${comp.name} - $${comp.price.toFixed(2)}</li>`;
            });
            
            html += `</ul>
                <div class="item-total">$${item.price.toFixed(2)}</div>
            </div>`;
        } else {
            html += `<div class="cart-item">
                <h4>${item.name}</h4>
                <div>Quantity: ${item.quantity}</div>
                <div class="item-total">$${(item.price * item.quantity).toFixed(2)}</div>
            </div>`;
        }
    });
    html += '</div>';
    document.getElementById('cart-items').innerHTML = html;
    document.getElementById('cart-total').textContent = cart.total.toFixed(2);
}
*/

function disableCheckout() {
    document.querySelector('.btn-block').disabled = true;
    document.querySelector('.btn-block').textContent = 'Cart is Empty';
}

function processCheckout() {
    const customerName = document.getElementById('customerName').value;
    const email = document.getElementById('email').value;
    const phone = document.getElementById('phone').value;
    const address = document.getElementById('address').value;
    const paymentMethod = document.getElementById('paymentMethod').value;
    
    // Validate inputs
    if (!customerName || !email || !phone || !address) {
        alert('Please fill in all required fields');
        return;
    }
    
    if (!validateEmail(email)) {
        alert('Please enter a valid email address');
        return;
    }
    
    if (!validatePhone(phone)) {
        alert('Please enter a valid phone number');
        return;
    }
    
    // Create order data
    const orderData = {
        customerName: customerName,
        email: email,
        phone: phone,
        address: address,
        paymentMethod: paymentMethod,
        timestamp: new Date().toISOString()
    };
    
    // Show loading
    const button = document.querySelector('.btn-block');
    const originalText = button.textContent;
    button.textContent = 'Processing...';
    button.disabled = true;
    
    // Send order to server
    const formData = new FormData();
    formData.append('order_data', JSON.stringify(orderData));
    
    fetch('api/checkout.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            document.querySelector('.checkout-container').innerHTML = `
                <div class="checkout-success text-center">
                    <h2>🎉 Order Successful!</h2>
                    <div class="success-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <p>Thank you for your purchase, ${customerName}!</p>
                    <p>Your order ID is: <strong>${data.order_id}</strong></p>
                    <p>We have sent a confirmation email to ${email}</p>
                    <p>Estimated delivery: 3-5 business days</p>
                    <button onclick="window.location.href='index.php'" class="btn btn-primary">
                        Continue Shopping
                    </button>
                </div>
            `;
            
            // Clear cart after successful order
            fetch('../api/clear-cart.php', { method: 'POST' });
        } else {
            alert('Error: ' + data.message);
            button.textContent = originalText;
            button.disabled = false;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred during checkout. Please try again.');
        button.textContent = originalText;
        button.disabled = false;
    });
}

function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

function validatePhone(phone) {
    const re = /^[\+]?[1-9][\d]{0,15}$/;
    return re.test(phone.replace(/[\s\-\(\)]/g, ''));
}
</script>

<style>
.checkout-container {
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
}

.cart-items-list {
    margin-bottom: 30px;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 15px;
}

.cart-item {
    padding: 15px 0;
    border-bottom: 1px solid #eee;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.cart-item:last-child {
    border-bottom: none;
}

.cart-item h4 {
    margin: 0;
    flex-grow: 1;
}

.item-total {
    font-weight: bold;
    font-size: 18px;
}

.build-item {
    flex-direction: column;
    align-items: flex-start;
}

.build-item ul {
    margin: 10px 0;
    padding-left: 20px;
}

.build-item .item-total {
    align-self: flex-end;
    margin-top: 10px;
}

.checkout-total {
    text-align: right;
    font-size: 24px;
    margin: 20px 0;
    padding: 20px;
    background-color: #f8f9fa;
    border-radius: 8px;
}

.shipping-form {
    background-color: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 20px;
}

.shipping-form h3{
    margin-bottom: 15px;
}

.form-group {
    margin-bottom: 15px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
}

.form-control {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 16px;
}

.checkout-success {
    padding: 40px;
    background-color: #f8f9fa;
    border-radius: 10px;
    margin-top: 20px;
}

.success-icon {
    font-size: 80px;
    color: #28a745;
    margin: 20px 0;
}

.btn-block {
    padding: 15px;
    font-size: 18px;
    font-weight: bold;
}

.btn-success {
    background-color: #28a745;
    border-color: #28a745;
}

.btn-success:hover {
    background-color: #218838;
    border-color: #1e7e34;
}
</style>