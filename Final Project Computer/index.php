<?php
require_once 'config/config.php';
require_once 'includes/auth.php';

$db = new Database();
$pdo = $db->connect();
$auth = new Auth($pdo);

$page = $_GET['page'] ?? 'home';
$page = preg_replace('/[^a-zA-Z0-9_-]/', '', $page);

if ($page === 'logout') {
    require 'pages/logout.php';
    exit;
}

$allowedPages = ['home', 'login', 'register', 'dashboard', 'pet-profile', 'vet-finder', 'community', 'account'];

if (!in_array($page, $allowedPages)) {
    $page = 'home';
}

$protectedPages = ['dashboard', 'pet-profile', 'vet-finder', 'community'];
if (in_array($page, $protectedPages) && !$auth->isLoggedIn()) {
    header('Location: index.php?page=login');
    exit;
}

include 'pages/includes/header.php';
include "pages/$page.php";
include 'pages/includes/footer.php'; 