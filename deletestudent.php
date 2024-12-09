<?php
session_start();
if (!isset($_SESSION['name'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $studid = $_POST['id'];

    try {
       
        $pdo = new PDO("mysql:host=localhost:3306;dbname=usjr", "root", "root");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        
        $stmt = $pdo->prepare("SELECT * FROM students WHERE studid = ?");
        $stmt->execute([$studid]);
        $student = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$student) {
           
            $_SESSION['error'] = "Student not found.";
            header("Location: home.php");
            exit();
        }

     
        $stmt = $pdo->prepare("DELETE FROM students WHERE studid = ?");
        $stmt->execute([$studid]);

        if ($stmt->rowCount() > 0) {
            $_SESSION['success'] = "Student with ID $studid has been successfully deleted.";
        } else {
            $_SESSION['error'] = "Failed to delete student. Please try again.";
        }
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        $_SESSION['error'] = "An error occurred while deleting the student.";
    }

   
    header("Location: home.php");
    exit();
} else {
   
    $_SESSION['error'] = "Invalid request.";
    header("Location: home.php");
    exit();
}
