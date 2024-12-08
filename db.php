<?php
$host = 'localhost';
$dbname = 'usjr';
$db_user = 'root';
$db_password = '';

try {
    $db = new PDO("mysql:host=$host;dbname=$dbname", $db_user, $db_password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
