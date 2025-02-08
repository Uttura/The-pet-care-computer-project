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

    $post = $_POST['post'] ?? '';
    
    if (!in_array($post, ['pet_owner', 'veterinarian'])) {
        throw new Exception('Invalid profession selected');
    }

    // Update the post field
    $stmt = $pdo->prepare("UPDATE users SET post = ? WHERE user_id = ?");
    if (!$stmt->execute([$post, $_SESSION['user_id']])) {
        throw new Exception('Failed to update profession');
    }

    // Update session
    $_SESSION['post'] = $post;

    echo json_encode(['success' => true]);

} catch (Exception $e) {
    error_log('Update profession error: ' . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'message' => 'Error updating profession: ' . $e->getMessage()
    ]);
} 