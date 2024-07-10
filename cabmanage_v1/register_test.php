<?php
// Include the necessary files
require_once 'config.php';
require_once 'auth.php';

// Initialize the Auth class
$db = $link;
$auth = new Auth($db);

// Register a new user for testing
$register_result = $auth->register('Assitance1', 'L3roui', '1234567890', 'A1@example.com', 'password123', 'Assistant');
if ($register_result) {
    echo "User registered successfully.<br>";
} else {
    echo "User registration failed.<br>";
}
?>

