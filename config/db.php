<?php
// config/db.php
$host = 'localhost';
$dbname = 'payak_db_name'; // CHANGE THIS to your actual DB name
$user = 'root';
$pass = ''; // Leave empty for Windows XAMPP compatibility

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
