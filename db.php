<?php
$host = 'localhost';
$dbname = 'blog_db';
$username = 'blog_user';
$password = 'StrongPassword123!';

try {
    // Establishing the PDO connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    
    // PDO will throw any exception if there is any  database error
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Disable emulated prepared statements, we will do them ourselvs manually
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
