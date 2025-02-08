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
    // Validate input
    $name = $_POST['name'] ?? '';
    $species = $_POST['species'] ?? '';
    $breed = $_POST['breed'] ?? '';
    $age = $_POST['age'] ?? '';

    if (empty($name) || empty($species) || empty($breed) || empty($age)) {
        throw new Exception('All fields are required');
    }

    $image = null;
    // Handle photo upload
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/../uploads/pets/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Validate file type
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $fileType = $_FILES['photo']['type'];
        if (!in_array($fileType, $allowedTypes)) {
            throw new Exception('Invalid file type. Only JPG, PNG and GIF are allowed.');
        }

        $fileName = uniqid() . '_' . basename($_FILES['photo']['name']);
        $uploadFile = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['photo']['tmp_name'], $uploadFile)) {
            $image = 'uploads/pets/' . $fileName;
        } else {
            throw new Exception('Failed to upload image');
        }
    }

    // Insert pet into database
    $stmt = $pdo->prepare("
        INSERT INTO pets (user_id, name, species, breed, age, profile_image) 
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    
    if (!$stmt->execute([$_SESSION['user_id'], $name, $species, $breed, $age, $image])) {
        throw new Exception('Failed to add pet');
    }

    echo json_encode(['success' => true]);

} catch (Exception $e) {
    error_log('Add pet error: ' . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'message' => 'Error adding pet: ' . $e->getMessage()
    ]);
} 