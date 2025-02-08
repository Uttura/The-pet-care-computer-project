<?php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'pet_care_db');
define('DB_USER', 'root');
define('DB_PASS', '');

// Google Maps Configuration
define('MAPS_API_KEY', 'your_google_maps_api_key');

// Database Connection Class
class Database {
    private $host = DB_HOST;
    private $dbname = DB_NAME;
    private $username = DB_USER;
    private $password = DB_PASS;
    private $conn;

    public function connect() {
        try {
            // First try to connect without database name
            $this->conn = new PDO(
                "mysql:host={$this->host}",
                $this->username,
                $this->password,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );

            // Create database if it doesn't exist
            $this->conn->exec("CREATE DATABASE IF NOT EXISTS {$this->dbname}");
            
            // Connect to the specific database
            $this->conn = new PDO(
                "mysql:host={$this->host};dbname={$this->dbname}",
                $this->username,
                $this->password,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );

            // Create tables if they don't exist
            $this->createTables();
            
            return $this->conn;
        } catch(PDOException $e) {
            error_log('Database connection failed: ' . $e->getMessage());
            throw new Exception('Database connection failed. Please check your configuration.');
        }
    }

    private function createTables() {
        // Create users table
        $this->conn->exec("CREATE TABLE IF NOT EXISTS users (
            user_id INT PRIMARY KEY AUTO_INCREMENT,
            email VARCHAR(255) UNIQUE NOT NULL,
            password_hash VARCHAR(255) NOT NULL,
            full_name VARCHAR(100) NOT NULL,
            phone VARCHAR(20),
            profile_image VARCHAR(255),
            post ENUM('pet_owner', 'veterinarian') DEFAULT 'pet_owner',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        // Create pets table
        $this->conn->exec("CREATE TABLE IF NOT EXISTS pets (
            pet_id INT PRIMARY KEY AUTO_INCREMENT,
            user_id INT NOT NULL,
            name VARCHAR(100) NOT NULL,
            species VARCHAR(50) NOT NULL,
            breed VARCHAR(100),
            birth_date DATE,
            profile_image VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        // Create vets table
        $this->conn->exec("CREATE TABLE IF NOT EXISTS vets (
            vet_id INT PRIMARY KEY AUTO_INCREMENT,
            clinic_name VARCHAR(255) NOT NULL,
            address TEXT NOT NULL,
            phone VARCHAR(20),
            email VARCHAR(255),
            rating DECIMAL(3,1) DEFAULT 0.0,
            latitude DECIMAL(10,8),
            longitude DECIMAL(11,8),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        // Create appointments table
        $this->conn->exec("CREATE TABLE IF NOT EXISTS appointments (
            appointment_id INT PRIMARY KEY AUTO_INCREMENT,
            pet_id INT NOT NULL,
            vet_id INT NOT NULL,
            appointment_date DATETIME NOT NULL,
            reason TEXT,
            status ENUM('scheduled', 'completed', 'cancelled') DEFAULT 'scheduled',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (pet_id) REFERENCES pets(pet_id) ON DELETE CASCADE,
            FOREIGN KEY (vet_id) REFERENCES vets(vet_id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        // Create posts table
        $this->conn->exec("CREATE TABLE IF NOT EXISTS posts (
            post_id INT PRIMARY KEY AUTO_INCREMENT,
            user_id INT NOT NULL,
            content TEXT NOT NULL,
            image VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    }
}

// Site configuration
define('SITE_URL', 'http://localhost/Final Project Computer');
define('UPLOAD_PATH', 'uploads/'); 