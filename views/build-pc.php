<?php
if (!isLoggedIn()) {
    redirect('index.php?page=login');
}
?>

<div class="container">
    <h2>Build Your PC</h2>
    
    <div class="build-pc-container">
        <div class="component-selection">
            <div class="component-group">
                <h3>Processor (CPU)</h3>
                <select id="cpu" class="component-select" data-category="Processor">
                    <option value="">Select CPU</option>
                </select>
            </div>
            
            <div class="component-group">
                <h3>Motherboard</h3>
                <select id="motherboard" class="component-select" data-category="Motherboard">
                    <option value="">Select Motherboard</option>
                </select>
            </div>
            
            <div class="component-group">
                <h3>Graphics Card (GPU)</h3>
                <select id="gpu" class="component-select" data-category="Graphics Card">
                    <option value="">Select GPU</option>
                </select>
            </div>
            
            <div class="component-group">
                <h3>Memory (RAM)</h3>
                <select id="ram" class="component-select" data-category="Memory">
                    <option value="">Select RAM</option>
                </select>
            </div>
            
            <div class="component-group">
                <h3>Storage</h3>
                <select id="storage" class="component-select" data-category="Storage">
                    <option value="">Select Storage</option>
                </select>
            </div>
            
            <div class="component-group">
                <h3>Power Supply (PSU)</h3>
                <select id="psu" class="component-select" data-category="Power Supply">
                    <option value="">Select PSU</option>
                </select>
            </div>
            
            <div class="component-group">
                <h3>Case</h3>
                <select id="case" class="component-select" data-category="Case">
                    <option value="">Select Case</option>
                </select>
            </div>
        </div>
        
        <div class="build-summary">
            <h3>Build Summary</h3>
            <div id="selectedComponents"></div>
            <div class="total-price">
                <strong>Total Price: $<span id="totalPrice">0.00</span></strong>
            </div>
            <!-- <button id="proceedCheckout" onclick="buyNow(<?php echo $product['id']; ?>, '<?php echo addslashes($product['name']); ?>', <?php echo $product['price']; ?>)">Proceed to Checkout</button> -->
        </div>
    </div>
</div>

<script>
let selectedComponents = {};
let componentProducts = {};

document.addEventListener('DOMContentLoaded', function() {
    loadComponentsByCategory();
});

function loadComponentsByCategory() {
    fetch('api/get-products.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const products = data.products;
                
                // Group products by category
                products.forEach(product => {
                    if (!componentProducts[product.category]) {
                        componentProducts[product.category] = [];
                    }
                    componentProducts[product.category].push(product);
                });
                
                document.querySelectorAll('.component-select').forEach(select => {
                    const category = select.dataset.category;
                    if (componentProducts[category]) {
                        componentProducts[category].forEach(product => {
                            const option = document.createElement('option');
                            option.value = product.id;
                            option.textContent = `${product.name} - $${parseFloat(product.price).toFixed(2)}`;
                            option.dataset.price = product.price;
                            option.dataset.name = product.name;
                            select.appendChild(option);
                        });
                    }
                });
            }
        });
}

document.querySelectorAll('.component-select').forEach(select => {
    select.addEventListener('change', function() {
        const componentType = this.id;
        const selectedOption = this.options[this.selectedIndex];
        
        if (selectedOption.value) {
            selectedComponents[componentType] = {
                id: selectedOption.value,
                name: selectedOption.dataset.name,
                price: parseFloat(selectedOption.dataset.price)
            };
        } else {
            delete selectedComponents[componentType];
        }
        
        updateBuildSummary();
    });
});

function updateBuildSummary() {
    const summaryDiv = document.getElementById('selectedComponents');
    const totalPrice = Object.values(selectedComponents).reduce((sum, comp) => sum + comp.price, 0);
    
    summaryDiv.innerHTML = Object.entries(selectedComponents).map(([type, comp]) => `
        <div class="summary-item">
            <span>${type.toUpperCase()}: ${comp.name}</span>
            <span>$${comp.price.toFixed(2)}</span>
        </div>
    `).join('');
    
    document.getElementById('totalPrice').textContent = totalPrice.toFixed(2);
    
    // Enable checkout if at least 3 components selected
    const checkoutBtn = document.getElementById('proceedCheckout');
    checkoutBtn.disabled = Object.keys(selectedComponents).length < 3;
}
document.getElementById('proceedCheckout').addEventListener('click', function() {
    // Convert selectedComponents to array format
    const componentsArray = Object.values(selectedComponents);
    
    const formData = new FormData();
    formData.append('build_components', JSON.stringify(componentsArray));
    
    fetch('api/add-to-cart.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.href = 'index.php?page=checkout';
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while adding to cart');
    });
});
</script>