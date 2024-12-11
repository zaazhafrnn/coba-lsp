<?php
// Database Configuration
$host = '127.0.0.1';
$dbname = 'inventory_db';
$username = 'user-baru';
$password = 'password';

try {
    // Create PDO connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    
    // Set error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    // Handle connection error
    die("Database Connection Failed: " . $e->getMessage());
}
?>