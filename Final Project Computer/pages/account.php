<?php
// Check if user is logged in
if (!$auth->isLoggedIn()) {
    header('Location: ?page=login');
    exit();
}

// Get user information
try {
    // First verify we have a user_id in session
    if (!isset($_SESSION['user_id'])) {
        // If no user_id in session, redirect to login
        $auth->logout(); // Clear any invalid session data
        header('Location: ?page=login');
        exit();
    }

    $stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        // If user not found in database, clear session and redirect
        $auth->logout();
        header('Location: ?page=login?error=account_not_found');
        exit();
    }
} catch (PDOException $e) {
    error_log('Account page error: ' . $e->getMessage());
    echo "<div class='alert alert-danger'>An error occurred. Please try again later.</div>";
    exit;
} catch (Exception $e) {
    error_log('Account page error: ' . $e->getMessage());
    $auth->logout();
    header('Location: ?page=login?error=session_expired');
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $fullName = $_POST['full_name'] ?? '';
        $email = $_POST['email'] ?? '';
        $phone = $_POST['phone'] ?? '';
        
        // Update user information
        $stmt = $pdo->prepare("
            UPDATE users 
            SET full_name = ?, email = ?, phone = ?
            WHERE user_id = ?
        ");
        
        if ($stmt->execute([$fullName, $email, $phone, $_SESSION['user_id']])) {
            // Refresh user data after update
            $stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            echo "<div class='alert alert-success'>Account updated successfully!</div>";
        }
    } catch (PDOException $e) {
        error_log($e->getMessage());
        echo "<div class='alert alert-danger'>Error updating account.</div>";
    }
}
?>

<div class="account-container">
    <h2><i class="fas fa-user-circle"></i> Account Information</h2>
    
    <div class="account-grid">
        <!-- Current Information -->
        <div class="info-card">
            <h3>Current Information</h3>
            <table class="info-table">
                <tr>
                    <th>Full Name</th>
                    <td><?php echo htmlspecialchars($user['full_name'] ?? ''); ?></td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td><?php echo htmlspecialchars($user['email'] ?? ''); ?></td>
                </tr>
                <tr>
                    <th>Phone</th>
                    <td><?php echo htmlspecialchars($user['phone'] ?? 'Not set'); ?></td>
                </tr>
                <tr>
                    <th>Member Since</th>
                    <td><?php echo isset($user['created_at']) ? date('M d, Y', strtotime($user['created_at'])) : 'N/A'; ?></td>
                </tr>
            </table>
        </div>

        <!-- Edit Information -->
        <div class="edit-card">
            <h3>Edit Information</h3>
            <form class="edit-form" method="POST">
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="full_name" value="<?php echo htmlspecialchars($user['full_name'] ?? ''); ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Phone</label>
                    <input type="tel" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save Changes
                </button>
            </form>
        </div>
    </div>

    <!-- Professional Role Section -->
    <div class="account-section profession-section">
        <h3>Professional Role</h3>
        <div class="profession-selection">
            <?php
            $currentPost = $user['post'] ?? 'pet_owner';
            ?>
            <label class="profession-option" data-value="pet_owner">
                <input type="radio" name="post" value="pet_owner" 
                    <?php echo $currentPost === 'pet_owner' ? 'checked' : ''; ?>>
                <div class="option-content">
                    <i class="fas fa-paw"></i>
                    <span>Pet Owner</span>
                </div>
            </label>
            <label class="profession-option" data-value="veterinarian">
                <input type="radio" name="post" value="veterinarian"
                    <?php echo $currentPost === 'veterinarian' ? 'checked' : ''; ?>>
                <div class="option-content">
                    <i class="fas fa-user-md"></i>
                    <span>Veterinarian</span>
                </div>
            </label>
        </div>
        <button onclick="updateProfession()" class="btn-update">Update Profession</button>
    </div>
</div>

<script>
// Add click handlers for profession options
document.querySelectorAll('.profession-option').forEach(option => {
    option.addEventListener('click', function() {
        // Update radio button
        const radio = this.querySelector('input[type="radio"]');
        radio.checked = true;
    });
});

function updateProfession() {
    const selectedPost = document.querySelector('input[name="post"]:checked').value;
    const formData = new FormData();
    formData.append('post', selectedPost);

    fetch('api/update_post.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update UI to reflect the change
            document.querySelectorAll('.profession-option').forEach(option => {
                const isSelected = option.dataset.value === selectedPost;
                option.querySelector('input[type="radio"]').checked = isSelected;
            });
            alert('Profession updated successfully!');
            location.reload();
        } else {
            alert(data.message || 'Error updating profession');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating profession');
    });
}
</script>

<style>
/* Remove user-type related styles and keep only profession styles */
.profession-section {
    background: white;
    border-radius: 10px;
    padding: 20px;
    margin-top: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.profession-selection {
    display: flex;
    gap: 20px;
    margin: 20px 0;
}

.profession-option {
    flex: 1;
    cursor: pointer;
    position: relative;
}

.profession-option .option-content {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 15px;
    border: 2px solid #ddd;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.profession-option:hover .option-content {
    border-color: #2ecc71;
}

.profession-option input[type="radio"] {
    position: absolute;
    opacity: 0;
}

.profession-option input[type="radio"]:checked + .option-content {
    border-color: #2ecc71;
    background: #f0fff4;
}

.profession-option input[type="radio"]:checked + .option-content i,
.profession-option input[type="radio"]:checked + .option-content span {
    color: #2ecc71;
}

.profession-option i {
    font-size: 24px;
    color: #666;
}

.btn-update {
    background: #2ecc71;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 5px;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    margin-top: 10px;
}

.btn-update:hover {
    background: #27ae60;
}

/* Keep other existing styles */
.account-container {
    max-width: 1000px;
    margin: 0 auto;
    padding: 20px;
}

.account-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 30px;
    margin-top: 20px;
}

.info-card, .edit-card {
    background: white;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.info-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 15px;
}

.info-table th, .info-table td {
    padding: 12px;
    border-bottom: 1px solid #eee;
    text-align: left;
}

.info-table th {
    width: 40%;
    color: #666;
}

.edit-form .form-group {
    margin-bottom: 15px;
}

.edit-form label {
    display: block;
    margin-bottom: 5px;
    color: #666;
}

.edit-form input {
    width: 100%;
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 5px;
}

.btn-primary {
    background: #2ecc71;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 5px;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.btn-primary:hover {
    background: #27ae60;
}

.alert {
    padding: 15px;
    border-radius: 5px;
    margin-bottom: 20px;
}

.alert-success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.alert-danger {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

@media (max-width: 768px) {
    .account-grid {
        grid-template-columns: 1fr;
    }
}

.post-section {
    background: white;
    border-radius: 10px;
    padding: 20px;
    margin-top: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.post-selection {
    display: flex;
    gap: 20px;
    margin: 20px 0;
}

.post-option {
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

.post-option:hover {
    border-color: #2ecc71;
}

.post-option.selected {
    border-color: #2ecc71;
    background: #f0fff4;
}

.post-option input[type="radio"] {
    display: none;
}

.post-option i {
    font-size: 24px;
    color: #666;
}

.post-option.selected i,
.post-option.selected span {
    color: #2ecc71;
}

.btn-update {
    background: #2ecc71;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 5px;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    margin-top: 10px;
}

.btn-update:hover {
    background: #27ae60;
}
</style> 