<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    error_log('Login attempt - Email: ' . ($_POST['email'] ?? 'not set'));
    
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    try {
        if ($auth->login($email, $password)) {
            header('Location: index.php?page=dashboard');
            exit;
        } else {
            $error = "Invalid email or password";
        }
    } catch (Exception $e) {
        error_log('Login debug error: ' . $e->getMessage());
        $error = "Login failed. Please try again.";
    }
}
?>

<div class="login-container">
    <h2>Login</h2>
    <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
    
    <form method="POST" class="login-form">
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>
        </div>
        
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
        </div>
        
        <button type="submit" class="btn-login">Login</button>
    </form>
    
    <p class="register-link">Don't have an account? <a href="?page=register">Register here</a></p>
</div>

<style>
.login-container {
    max-width: 400px;
    margin: 40px auto;
    padding: 20px;
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.error {
    color: #dc3545;
    background: #f8d7da;
    padding: 10px;
    border-radius: 5px;
    margin-bottom: 20px;
}

.form-group {
    margin-bottom: 15px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    color: #666;
}

.form-group input {
    width: 100%;
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 5px;
}

.btn-login {
    width: 100%;
    padding: 10px;
    background: #2ecc71;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

.btn-login:hover {
    background: #27ae60;
}

.register-link {
    text-align: center;
    margin-top: 20px;
}
</style> 