<?php
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/auth.php';

header('Content-Type: application/json');

if (!$auth->isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    $userType = $_POST['user_type'] ?? '';
    
    if (!in_array($userType, ['pet_owner', 'veterinarian'])) {
        throw new Exception('Invalid account type selected');
    }

    $stmt = $pdo->prepare("UPDATE users SET user_type = ? WHERE user_id = ?");
    if (!$stmt->execute([$userType, $_SESSION['user_id']])) {
        throw new Exception('Failed to update account type');
    }

    // Update user_type in session
    $_SESSION['user_type'] = $userType;

    echo json_encode(['success' => true]);

} catch (Exception $e) {
    error_log('Update user type error: ' . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'message' => 'Error updating account type: ' . $e->getMessage()
    ]);
} 