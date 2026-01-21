<?php
require_once 'controllers/ProductController.php';
$productController = new ProductController();

$productId = isset($_GET['id']) ? intval($_GET['id']) : 0;
$product = $productController->getProductById($productId);

if (!$product) {
    header('Location: index.php?page=admin');
    exit;
}
?>

<div class="container">
    <h2>Edit Product</h2>
    
    <div class="admin-form-container">
        <form id="editProductForm" enctype="multipart/form-data">
            <input type="hidden" id="productId" value="<?php echo $product['id']; ?>">
            
            <div class="form-group">
                <label for="productName">Product Name</label>
                <input type="text" id="productName" name="productName" 
                       value="<?php echo htmlspecialchars($product['name']); ?>" required>
                <span class="error" id="nameError"></span>
            </div>
            
            <div class="form-group">
                <label for="category">Category</label>
                <select id="category" name="category" required>
                    <option value="">Select Category</option>
                    <option value="Processor" <?php echo $product['category'] == 'Processor' ? 'selected' : ''; ?>>Processor</option>
                    <option value="Motherboard" <?php echo $product['category'] == 'Motherboard' ? 'selected' : ''; ?>>Motherboard</option>
                    <option value="Graphics Card" <?php echo $product['category'] == 'Graphics Card' ? 'selected' : ''; ?>>Graphics Card</option>
                    <option value="Memory" <?php echo $product['category'] == 'Memory' ? 'selected' : ''; ?>>Memory</option>
                    <option value="Storage" <?php echo $product['category'] == 'Storage' ? 'selected' : ''; ?>>Storage</option>
                    <option value="Power Supply" <?php echo $product['category'] == 'Power Supply' ? 'selected' : ''; ?>>Power Supply</option>
                    <option value="Case" <?php echo $product['category'] == 'Case' ? 'selected' : ''; ?>>Case</option>
                    <option value="Cooling" <?php echo $product['category'] == 'Cooling' ? 'selected' : ''; ?>>Cooling</option>
                </select>
                <span class="error" id="categoryError"></span>
            </div>
            
            <div class="form-group">
                <label for="price">Price ($)</label>
                <input type="number" id="price" name="price" step="0.01" min="0" 
                       value="<?php echo $product['price']; ?>" required>
                <span class="error" id="priceError"></span>
            </div>
            
            <div class="form-group">
                <label>Current Image</label>
                <div class="current-image">
                    <img src="assets/images/products/<?php echo htmlspecialchars($product['image']); ?>" 
                         alt="<?php echo htmlspecialchars($product['name']); ?>" 
                         style="max-width: 200px; max-height: 200px;">
                </div>
            </div>
            
            <div class="form-group">
                <label for="image">Change Image (Optional)</label>
                <input type="file" id="image" name="image" accept="image/*">
                <span class="error" id="imageError"></span>
                <small>Leave empty to keep current image</small>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Update Product</button>
                <a href="index.php?page=admin" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
        <div id="productMessage" class="message"></div>
    </div>
</div>

<script>
document.getElementById('editProductForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const productId = document.getElementById('productId').value;
    const productName = document.getElementById('productName').value.trim();
    const category = document.getElementById('category').value;
    const price = document.getElementById('price').value;
    const imageFile = document.getElementById('image').files[0];
    
    document.querySelectorAll('.error').forEach(el => el.textContent = '');
    
    let isValid = true;
    
    if (productName === '') {
        document.getElementById('nameError').textContent = 'Product name is required';
        isValid = false;
    } else if (productName.length < 3) {
        document.getElementById('nameError').textContent = 'Product name must be at least 3 characters';
        isValid = false;
    }
    
    if (category === '') {
        document.getElementById('categoryError').textContent = 'Please select a category';
        isValid = false;
    }
    
    if (price === '' || parseFloat(price) <= 0) {
        document.getElementById('priceError').textContent = 'Please enter a valid price';
        isValid = false;
    }
    
    if (imageFile) {
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        if (!allowedTypes.includes(imageFile.type)) {
            document.getElementById('imageError').textContent = 'Only JPG, PNG, and GIF images are allowed';
            isValid = false;
        } else if (imageFile.size > 5 * 1024 * 1024) {
            document.getElementById('imageError').textContent = 'Image size must be less than 5MB';
            isValid = false;
        }
    }
    
    if (!isValid) return;
    
    const formData = new FormData();
    formData.append('id', productId);
    formData.append('productName', productName);
    formData.append('category', category);
    formData.append('price', price);
    if (imageFile) {
        formData.append('image', imageFile);
    }
    
    fetch('api/update-product.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.text();
    })
    .then(text => {
        let data;
        try {
            data = JSON.parse(text);
        } catch (e) {
            console.error('Response text:', text);
            throw new Error('Invalid JSON response');
        }
        return data;
    })
    .then(data => {
        const messageDiv = document.getElementById('productMessage');
        if (data.success) {
            messageDiv.className = 'message success';
            messageDiv.textContent = 'Product updated successfully!';
            setTimeout(() => {
                window.location.href = 'index.php?page=admin';
            }, 1500);
        } else {
            messageDiv.className = 'message error';
            messageDiv.textContent = data.message;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        const messageDiv = document.getElementById('productMessage');
        messageDiv.className = 'message error';
        messageDiv.textContent = 'An error occurred: ' + error.message;
    });
});
</script>