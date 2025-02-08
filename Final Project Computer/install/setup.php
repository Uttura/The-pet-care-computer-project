<?php
require_once '../config/config.php';

try {
    // Create database connection without database name first
    $conn = new PDO("mysql:host=" . DB_HOST, DB_USER, DB_PASS);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create database if it doesn't exist
    $conn->exec("CREATE DATABASE IF NOT EXISTS " . DB_NAME);
    
    // Select the database
    $conn->exec("USE " . DB_NAME);
    
    // Create users table
    $sql = "CREATE TABLE IF NOT EXISTS users (
        user_id INT PRIMARY KEY AUTO_INCREMENT,
        email VARCHAR(255) UNIQUE NOT NULL,
        password_hash VARCHAR(255) NOT NULL,
        full_name VARCHAR(100) NOT NULL,
        phone VARCHAR(20),
        profile_image VARCHAR(255),
        post ENUM('pet_owner', 'veterinarian') DEFAULT 'pet_owner',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    $conn->exec($sql);

    // Create pets table
    $sql = "CREATE TABLE IF NOT EXISTS pets (
        pet_id INT PRIMARY KEY AUTO_INCREMENT,
        user_id INT NOT NULL,
        name VARCHAR(100) NOT NULL,
        species VARCHAR(50) NOT NULL,
        breed VARCHAR(100),
        birth_date DATE,
        profile_image VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    $conn->exec($sql);

    // Create vets table
    $sql = "CREATE TABLE IF NOT EXISTS vets (
        vet_id INT PRIMARY KEY AUTO_INCREMENT,
        clinic_name VARCHAR(255) NOT NULL,
        address TEXT NOT NULL,
        phone VARCHAR(20),
        email VARCHAR(255),
        rating DECIMAL(3,1) DEFAULT 0.0,
        latitude DECIMAL(10,8),
        longitude DECIMAL(11,8),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    $conn->exec($sql);

    // Create appointments table
    $sql = "CREATE TABLE IF NOT EXISTS appointments (
        appointment_id INT PRIMARY KEY AUTO_INCREMENT,
        pet_id INT NOT NULL,
        vet_id INT NOT NULL,
        appointment_date DATETIME NOT NULL,
        reason TEXT,
        status ENUM('scheduled', 'completed', 'cancelled') DEFAULT 'scheduled',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (pet_id) REFERENCES pets(pet_id) ON DELETE CASCADE,
        FOREIGN KEY (vet_id) REFERENCES vets(vet_id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    $conn->exec($sql);

    // Create health_records table
    $sql = "CREATE TABLE IF NOT EXISTS health_records (
        record_id INT PRIMARY KEY AUTO_INCREMENT,
        pet_id INT NOT NULL,
        record_type ENUM('vaccination', 'medication', 'checkup', 'surgery', 'other'),
        record_date DATE NOT NULL,
        description TEXT,
        next_due_date DATE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (pet_id) REFERENCES pets(pet_id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    $conn->exec($sql);

    // Create posts table
    $sql = "CREATE TABLE IF NOT EXISTS posts (
        post_id INT PRIMARY KEY AUTO_INCREMENT,
        user_id INT NOT NULL,
        content TEXT NOT NULL,
        image VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    $conn->exec($sql);

    echo "Setup completed successfully! <a href='../index.php'>Return to homepage</a>";
    
} catch(PDOException $e) {
    die("Setup Error: " . $e->getMessage());
}
?>

<style>
body {
    font-family: Arial, sans-serif;
    line-height: 1.6;
    margin: 20px;
    padding: 20px;
    background: #f5f5f5;
}

a {
    display: inline-block;
    margin-top: 20px;
    padding: 10px 20px;
    background: #4CAF50;
    color: white;
    text-decoration: none;
    border-radius: 5px;
}

a:hover {
    background: #45a049;
}
</style> 