<?php
require_once 'controllers/ProductController.php';
$productController = new ProductController();
$products = $productController->getAllProducts();
?>

<div class="container">
    <h2>Admin Dashboard</h2>
    
    <div class="admin-actions">
        <a href="index.php?page=add-product" class="btn btn-primary">Add New Product</a>
    </div>
    
    <div class="products-table-container">
        <h3>All Products</h3>
        <table class="products-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Date Added</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($products) > 0): ?>
                    <?php foreach ($products as $product): ?>
                        <tr id="product-row-<?php echo $product['id']; ?>">
                            <td><?php echo $product['id']; ?></td>
                            <td>
                                <img src="assets/images/products/<?php echo htmlspecialchars($product['image']); ?>" 
                                     alt="<?php echo htmlspecialchars($product['name']); ?>" 
                                     class="product-thumb">
                            </td>
                            <td><?php echo htmlspecialchars($product['name']); ?></td>
                            <td><?php echo htmlspecialchars($product['category']); ?></td>
                            <td>$<?php echo number_format($product['price'], 2); ?></td>
                            <td><?php echo date('Y-m-d', strtotime($product['created_at'])); ?></td>
                            <td class="action-buttons">
                                <a href="index.php?page=edit-product&id=<?php echo $product['id']; ?>" 
                                   class="btn btn-edit">Edit</a>
                                <button onclick="deleteProduct(<?php echo $product['id']; ?>)" 
                                        class="btn btn-delete">Delete</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center">No products found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div id="deleteModal" class="modal">
    <div class="modal-content">
        <h3>Confirm Delete</h3>
        <p>Are you sure you want to delete this product?</p>
        <div class="modal-actions">
            <button id="confirmDelete" class="btn btn-danger">Delete</button>
            <button id="cancelDelete" class="btn btn-secondary">Cancel</button>
        </div>
    </div>
</div>


<script>
let productIdToDelete = null;

function deleteProduct(id) {
    productIdToDelete = id;
    document.getElementById('deleteModal').style.display = 'block';
}

document.getElementById('confirmDelete').addEventListener('click', function() {
    if (!productIdToDelete) return;
    
    fetch('api/delete-product.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ id: productIdToDelete })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Remove the row from table
            const row = document.getElementById(`product-row-${productIdToDelete}`);
            if (row) {
                row.remove();
            }
            showMessage('Product deleted successfully!', 'success');
        } else {
            showMessage('Error: ' + data.message, 'error');
        }
        document.getElementById('deleteModal').style.display = 'none';
        productIdToDelete = null;
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('An error occurred', 'error');
        document.getElementById('deleteModal').style.display = 'none';
    });
});

document.getElementById('cancelDelete').addEventListener('click', function() {
    document.getElementById('deleteModal').style.display = 'none';
    productIdToDelete = null;
});

function showMessage(message, type) {
    const messageDiv = document.createElement('div');
    messageDiv.className = `message ${type}`;
    messageDiv.textContent = message;
    
    document.querySelector('.container').prepend(messageDiv);
    
    setTimeout(() => {
        messageDiv.remove();
    }, 3000);
}

window.onclick = function(event) {
    const modal = document.getElementById('deleteModal');
    if (event.target == modal) {
        modal.style.display = 'none';
    }
}
</script>