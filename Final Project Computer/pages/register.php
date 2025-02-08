<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Create data array for registration
        $registrationData = [
            'email' => $_POST['email'] ?? '',
            'password' => $_POST['password'] ?? '',
            'full_name' => $_POST['full_name'] ?? '',
            'phone' => $_POST['phone'] ?? '',
            'post' => $_POST['post'] ?? 'pet_owner'
        ];
        
        // Validate required fields
        if (empty($registrationData['email']) || 
            empty($registrationData['password']) || 
            empty($registrationData['full_name'])) {
            throw new Exception("All required fields must be filled out");
        }
        
        // Validate confirm password
        if ($_POST['password'] !== $_POST['confirm_password']) {
            throw new Exception("Passwords do not match");
        }
        
        // Register the user
        if ($auth->register($registrationData)) {
            // Log the user in automatically
            if ($auth->login($registrationData['email'], $registrationData['password'])) {
                header('Location: index.php?page=dashboard');
                exit;
            }
            header('Location: index.php?page=login&registered=1');
            exit;
        } else {
            throw new Exception("Registration failed");
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>

<div class="register-container">
    <h2>Create Account</h2>
    <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
    
    <form action="" method="POST" class="register-form">
        <div class="form-group">
            <label for="full_name">Full Name</label>
            <input type="text" id="full_name" name="full_name" required>
        </div>
        
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>
        </div>
        
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
        </div>
        
        <div class="form-group">
            <label for="confirm_password">Confirm Password</label>
            <input type="password" id="confirm_password" name="confirm_password" required>
        </div>

        <div class="form-group">
            <label for="phone">Phone Number</label>
            <input type="tel" id="phone" name="phone" required>
        </div>

        <div class="form-group">
            <label>I am a:</label>
            <div class="user-type-selection">
                <label class="type-option">
                    <input type="radio" name="user_type" value="pet_owner" checked>
                    <i class="fas fa-paw"></i>
                    Pet Owner
                </label>
                <label class="type-option">
                    <input type="radio" name="user_type" value="veterinarian">
                    <i class="fas fa-user-md"></i>
                    Veterinarian
                </label>
            </div>
        </div>
        
        <button type="submit" class="btn-register">Create Account</button>
    </form>
    <p class="login-link">Already have an account? <a href="?page=login">Login here</a></p>
</div>

<style>
/* Add to existing styles */
.role-selection {
    display: flex;
    gap: 20px;
    margin-top: 10px;
}

.role-option {
    flex: 1;
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 15px;
    border: 2px solid #ddd;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.role-option:hover {
    border-color: #2ecc71;
}

.role-option input[type="radio"] {
    display: none;
}

.role-option input[type="radio"]:checked + i {
    color: #2ecc71;
}

.role-option i {
    font-size: 20px;
    color: #666;
}

.role-option input[type="radio"]:checked ~ * {
    color: #2ecc71;
}
</style> 