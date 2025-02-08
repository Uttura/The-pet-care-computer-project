<?php
// Initialize auth with existing database connection
$auth->logout();

// Redirect to login page
header('Location: index.php?page=login');
exit; 