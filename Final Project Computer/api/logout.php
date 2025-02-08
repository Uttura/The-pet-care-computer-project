<?php
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/auth.php';

try {
    // Create database connection
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    // Initialize auth with database connection
    $auth = new Auth($pdo);
    
    // Perform logout
    $auth->logout();
    
    // Redirect to login page
    header('Location: ../index.php?page=login');
    exit;
    
} catch (PDOException $e) {
    error_log('Logout error: ' . $e->getMessage());
    header('Location: ../index.php?error=logout_failed');
    exit;
} 