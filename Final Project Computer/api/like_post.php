<?php
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/auth.php';

header('Content-Type: application/json');

if (!$auth->isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

$postId = $_GET['post_id'] ?? null;

if (!$postId) {
    echo json_encode(['success' => false, 'message' => 'Post ID required']);
    exit;
}

try {
    // Toggle like status (you'll need to create a likes table)
    echo json_encode(['success' => true, 'liked' => true]);
} catch (Exception $e) {
    error_log($e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error processing like']);
} 