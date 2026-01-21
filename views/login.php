<div class="container">
    <div class="auth-container">
        <h2>Login to Star Tech</h2>
        <form id="loginForm" class="auth-form">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
                <span class="error" id="usernameError"></span>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
                <span class="error" id="passwordError"></span>
            </div>
            <button type="submit" class="btn btn-primary">Login</button>
            <div class="auth-link">
                Don't have an account? <a href="index.php?page=signup">Sign Up</a>
            </div>
        </form>
        <div id="loginMessage" class="message"></div>
    </div>
</div>

<script>
document.getElementById('loginForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const username = document.getElementById('username').value.trim();
    const password = document.getElementById('password').value;
    
    // Client-side validation
    let isValid = true;
    
    document.getElementById('usernameError').textContent = '';
    document.getElementById('passwordError').textContent = '';
    
    if (username === '') {
        document.getElementById('usernameError').textContent = 'Username is required';
        isValid = false;
    }
    
    if (password === '') {
        document.getElementById('passwordError').textContent = 'Password is required';
        isValid = false;
    } else if (password.length < 6) {
        document.getElementById('passwordError').textContent = 'Password must be at least 6 characters';
        isValid = false;
    }
    
    if (!isValid) return;
    
    // AJAX request
    const formData = new FormData();
    formData.append('username', username);
    formData.append('password', password);
    
    fetch('api/login.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        const messageDiv = document.getElementById('loginMessage');
        if (data.success) {
            messageDiv.className = 'message success';
            messageDiv.textContent = 'Login successful! Redirecting...';
            setTimeout(() => {
                if (data.is_admin) {
                    window.location.href = 'index.php?page=admin';
                } else {
                    window.location.href = 'index.php?page=products';
                }
            }, 1000);
        } else {
            messageDiv.className = 'message error';
            messageDiv.textContent = data.message;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('loginMessage').textContent = 'An error occurred';
    });
});
</script>