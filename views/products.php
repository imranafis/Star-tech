<?php
// views/products.php
require_once 'controllers/ProductController.php';

$productController = new ProductController();
$categories = $productController->getCategories();

// Check if a category filter is selected
$selectedCategory = isset($_GET['category']) ? $_GET['category'] : '';

// Get products based on filter
if ($selectedCategory && $selectedCategory !== 'all') {
    $products = $productController->getProductsByCategory($selectedCategory);
} else {
    $products = $productController->getAllProducts();
}
?>

<div class="container">
    <h2>Our Products</h2>
    
    <!-- Category Filter -->
    <div class="category-filter">
        <label for="categorySelect">Filter by Category:</label>
        <select id="categorySelect" onchange="filterProducts()">
            <option value="all" <?php echo $selectedCategory === '' || $selectedCategory === 'all' ? 'selected' : ''; ?>>All Categories</option>
            <?php foreach ($categories as $category): ?>
                <option value="<?php echo htmlspecialchars($category); ?>" 
                    <?php echo $selectedCategory === $category ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($category); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    
    <!-- Products Grid -->
    <div class="products-grid">
        <?php if (count($products) > 0): ?>
            <?php foreach ($products as $product): ?>
             <!-- In products.php - change this section: -->
            <div class="product-card">
                <img src="assets/images/products/<?php echo $product['image']; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                <div class="category"><?php echo htmlspecialchars($product['category']); ?></div>
                <div class="price">$<?php echo number_format($product['price'], 2); ?></div>
                
                <!-- Replace Add to Cart with Buy Now button -->
                <button class="btn btn-success buy-now-btn" 
                        onclick="buyNow(<?php echo $product['id']; ?>, '<?php echo addslashes($product['name']); ?>', <?php echo $product['price']; ?>)">
                    Buy Now
                </button>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="no-products">No products found in this category.</p>
        <?php endif; ?>
    </div>
</div>
<script>
function filterProducts() {
    var category = document.getElementById('categorySelect').value;
    if (category === 'all') {
        window.location.href = 'index.php?page=products';
    } else {
        window.location.href = 'index.php?page=products&category=' + encodeURIComponent(category);
    }
}

// Function to buy product directly (no cart, direct checkout)
function buyNow(productId, productName, price) {
    console.log('Buying product:', productId, productName, price);
    
    // Show loading state
    const button = event.target;
    const originalText = button.textContent;
    button.textContent = 'Processing...';
    button.disabled = true;
    
    // Create form data
    const formData = new FormData();
    formData.append('product_id', productId);
    
    // Call buy-now API
    fetch('api/buy-now.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        // Check content type
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            return response.text().then(text => {
                console.error('Non-JSON response:', text.substring(0, 200));
                throw new Error('Server returned HTML instead of JSON');
            });
        }
        return response.json();
    })
    .then(data => {
        console.log('Buy now response:', data);
        
        if (data.success) {
            if (data.redirect) {
                // Redirect to checkout page
                window.location.href = data.redirect;
            } else {
                // Fallback to manual redirect
                window.location.href = 'index.php?page=checkout';
            }
        } else {
            alert('Error: ' + data.message);
            // Reset button
            button.textContent = originalText;
            button.disabled = false;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again. Check console for details.');
        // Reset button
        button.textContent = originalText;
        button.disabled = false;
    });
}

// Optional: If you want to keep some styling
</script>

<style>
/* Optional: Style the Buy Now button differently */
.buy-now-btn {
    background-color: #28a745;
    border-color: #28a745;
    width: 100%;
    padding: 10px;
    font-weight: bold;
}

.buy-now-btn:hover {
    background-color: #218838;
    border-color: #1e7e34;
}

.products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.product-card {
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 15px;
    text-align: center;
    transition: transform 0.3s;
    display: flex;
    flex-direction: column;
    height: 100%;
}

.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.product-card img {
    max-width: 100%;
    height: 150px;
    object-fit: contain;
    margin-bottom: 10px;
}

.product-card h3 {
    margin: 10px 0;
    font-size: 18px;
    flex-grow: 1;
}

.product-card .category {
    color: #666;
    font-size: 14px;
    margin: 5px 0;
}

.product-card .price {
    font-weight: bold;
    color: #333;
    font-size: 18px;
    margin: 10px 0;
    font-size: 20px;
}

.category-filter {
    margin: 20px 0;
    padding: 10px;
    background-color: #f5f5f5;
    border-radius: 5px;
}

.category-filter select {
    padding: 5px 10px;
    margin-left: 10px;
    border-radius: 4px;
    border: 1px solid #ddd;
}

.no-products {
    text-align: center;
    grid-column: 1 / -1;
    padding: 40px;
    color: #666;
}
</style>