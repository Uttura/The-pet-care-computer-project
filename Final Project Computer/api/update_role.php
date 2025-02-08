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
    // Initialize database connection
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    $role = $_POST['role'] ?? '';
    
    if (!in_array($role, ['pet_owner', 'veterinarian'])) {
        throw new Exception('Invalid role selected');
    }

    $stmt = $pdo->prepare("UPDATE users SET role = ? WHERE user_id = ?");
    if (!$stmt->execute([$role, $_SESSION['user_id']])) {
        throw new Exception('Failed to update role');
    }

    // Update role in session
    $_SESSION['role'] = $role;

    echo json_encode(['success' => true]);

} catch (Exception $e) {
    error_log('Update role error: ' . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'message' => 'Error updating role: ' . $e->getMessage()
    ]);
} 