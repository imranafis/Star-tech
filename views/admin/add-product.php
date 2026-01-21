<div class="container">
    <h2>Add New Product</h2>
    
    <div class="admin-form-container">
        <form id="addProductForm" enctype="multipart/form-data">
            <div class="form-group">
                <label for="productName">Product Name</label>
                <input type="text" id="productName" name="productName" required>
                <span class="error" id="nameError"></span>
            </div>
            
            <div class="form-group">
                <label for="category">Category</label>
                <select id="category" name="category" required>
                    <option value="">Select Category</option>
                    <option value="Processor">Processor</option>
                    <option value="Motherboard">Motherboard</option>
                    <option value="Graphics Card">Graphics Card</option>
                    <option value="Memory">Memory</option>
                    <option value="Storage">Storage</option>
                    <option value="Power Supply">Power Supply</option>
                    <option value="Case">Case</option>
                    <option value="Cooling">Cooling</option>
                </select>
                <span class="error" id="categoryError"></span>
            </div>
            
            <div class="form-group">
                <label for="price">Price ($)</label>
                <input type="number" id="price" name="price" step="0.01" min="0" required>
                <span class="error" id="priceError"></span>
            </div>
            
            <div class="form-group">
                <label for="image">Product Image</label>
                <input type="file" id="image" name="image" accept="image/*" required>
                <span class="error" id="imageError"></span>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Add Product</button>
                <a href="index.php?page=admin" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
        <div id="productMessage" class="message"></div>
    </div>
</div>

<script>
document.getElementById('addProductForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
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
    
    if (!imageFile) {
        document.getElementById('imageError').textContent = 'Please select an image';
        isValid = false;
    } else {
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        if (!allowedTypes.includes(imageFile.type)) {
            document.getElementById('imageError').textContent = 'Only JPG, PNG, and GIF images are allowed';
            isValid = false;
        } else if (imageFile.size > 5 * 1024 * 1024) { // 5MB
            document.getElementById('imageError').textContent = 'Image size must be less than 5MB';
            isValid = false;
        }
    }
    
    if (!isValid) return;
    
    const formData = new FormData();
    formData.append('productName', productName);
    formData.append('category', category);
    formData.append('price', price);
    formData.append('image', imageFile);
    
    fetch('api/add-product.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        const messageDiv = document.getElementById('productMessage');
        if (data.success) {
            messageDiv.className = 'message success';
            messageDiv.textContent = 'Product added successfully!';
            document.getElementById('addProductForm').reset();
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
        document.getElementById('productMessage').textContent = 'An error occurred';
    });
});
</script>