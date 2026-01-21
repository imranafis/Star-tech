<div class="container">
    <div class="auth-container">
        <h2>Sign Up for Star Tech</h2>
        <form id="signupForm" class="auth-form">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
                <span class="error" id="usernameError"></span>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
                <span class="error" id="emailError"></span>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
                <span class="error" id="passwordError"></span>
            </div>
            <div class="form-group">
                <label for="confirmPassword">Confirm Password</label>
                <input type="password" id="confirmPassword" name="confirmPassword" required>
                <span class="error" id="confirmPasswordError"></span>
            </div>
            <button type="submit" class="btn btn-primary">Sign Up</button>
            <div class="auth-link">
                Already have an account? <a href="index.php?page=login">Login</a>
            </div>
        </form>
        <div id="signupMessage" class="message"></div>
    </div>
</div>

<script>
document.getElementById('signupForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const username = document.getElementById('username').value.trim();
    const email = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirmPassword').value;
    
    // Clear previous errors
    document.querySelectorAll('.error').forEach(el => el.textContent = '');
    
    let isValid = true;
    
    // Username validation
    if (username === '') {
        document.getElementById('usernameError').textContent = 'Username is required';
        isValid = false;
    } else if (username.length < 3) {
        document.getElementById('usernameError').textContent = 'Username must be at least 3 characters';
        isValid = false;
    }
    
    // Email validation
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (email === '') {
        document.getElementById('emailError').textContent = 'Email is required';
        isValid = false;
    } else if (!emailRegex.test(email)) {
        document.getElementById('emailError').textContent = 'Invalid email format';
        isValid = false;
    }
    
    // Password validation
    if (password === '') {
        document.getElementById('passwordError').textContent = 'Password is required';
        isValid = false;
    } else if (password.length < 6) {
        document.getElementById('passwordError').textContent = 'Password must be at least 6 characters';
        isValid = false;
    }
    
    // Confirm password validation
    if (confirmPassword === '') {
        document.getElementById('confirmPasswordError').textContent = 'Please confirm password';
        isValid = false;
    } else if (password !== confirmPassword) {
        document.getElementById('confirmPasswordError').textContent = 'Passwords do not match';
        isValid = false;
    }
    
    if (!isValid) return;
    
    // AJAX request
    const formData = new FormData();
    formData.append('username', username);
    formData.append('email', email);
    formData.append('password', password);
    
    fetch('api/signup.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        const messageDiv = document.getElementById('signupMessage');
        if (data.success) {
            messageDiv.className = 'message success';
            messageDiv.textContent = data.message + ' Redirecting to login...';
            setTimeout(() => {
                window.location.href = 'index.php?page=login';
            }, 2000);
        } else {
            messageDiv.className = 'message error';
            messageDiv.textContent = data.message;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('signupMessage').textContent = 'An error occurred';
    });
});
</script>