<?php
if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit();
}

$studid = $_GET['id'];

try {
    $pdo = new PDO("mysql:host=localhost:3306;dbname=usjr", "root", "root");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("
        SELECT s.*, p.progcollid 
        FROM students s 
        JOIN programs p ON s.studprogid = p.progid 
        WHERE s.studid = ?
    ");
    $stmt->execute([$studid]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($student) {
        session_start();
        $_SESSION['edit_student'] = $student;
        header("Location: student.entry.php");
        exit();
    } else {
        echo "Student not found.";
    }
} catch (PDOException $e) {
    echo "Error: " . htmlspecialchars($e->getMessage());
}
?>
