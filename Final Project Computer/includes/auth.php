<?php
session_start();

class Auth {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    public function register($data) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO users (email, password_hash, full_name, phone, post) 
                VALUES (?, ?, ?, ?, ?)
            ");
            
            $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
            
            return $stmt->execute([
                $data['email'],
                $hashedPassword,
                $data['full_name'],
                $data['phone'],
                $data['post']
            ]);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }
    
    public function login($email, $password) {
        try {
            $sql = "SELECT * FROM users WHERE email = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user && password_verify($password, $user['password_hash'])) {
                // Start a new session
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }
                
                // Clear any existing session data
                $_SESSION = array();
                
                // Set new session data
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['post'] = $user['post'];
                $_SESSION['full_name'] = $user['full_name'];
                
                return true;
            }
            return false;
        } catch (PDOException $e) {
            error_log('Login error: ' . $e->getMessage());
            throw new Exception('Login failed');
        }
    }
    
    public function isLoggedIn() {
        // Check if session exists and has valid user data
        return isset($_SESSION['user_id']) && 
               isset($_SESSION['email']) && 
               !empty($_SESSION['user_id']) && 
               !empty($_SESSION['email']);
    }
    
    public function logout() {
        // Clear all session data
        $_SESSION = array();
        
        // Destroy the session cookie
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time()-3600, '/');
        }
        
        // Destroy the session
        session_destroy();
    }
} 