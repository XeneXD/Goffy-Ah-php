<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['name'])) {
    echo json_encode(['success' => false, 'error' => 'Not authenticated']);
    exit();
}

$firstName = $_POST['first_name'];
$lastName = $_POST['last_name'];
$middleName = $_POST['middle_name'];
$yearLevel = $_POST['year_level'] ?? ''; 
$selectedCollege = $_POST['college_id'];
$selectedProgram = $_POST['program_id'];
$studentId = $_POST['student_id'] ?? null;

try {
    $pdo = new PDO("mysql:host=localhost:3306;dbname=usjr", "root", "root");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($studentId) {
        $stmt = $pdo->prepare("
            UPDATE students
            SET studfirstname = ?, studlastname = ?, studmidname = ?, studprogid = ?, studcollid = ?, studyear = ?
            WHERE studid = ?
        ");
        $stmt->execute([$firstName, $lastName, $middleName, $selectedProgram, $selectedCollege, $yearLevel, $studentId]);
    } else {
        $stmt = $pdo->query("SELECT IFNULL(MAX(studid), 0) AS max_studid FROM students");
        $new_studid = $stmt->fetch(PDO::FETCH_ASSOC)['max_studid'] + 1;

        $stmt = $pdo->prepare("
            INSERT INTO students (studid, studfirstname, studlastname, studmidname, studprogid, studcollid, studyear)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$new_studid, $firstName, $lastName, $middleName, $selectedProgram, $selectedCollege, $yearLevel]);
    }

    echo json_encode(['success' => true]);
    exit();
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => htmlspecialchars($e->getMessage())]);
    exit();
}
?>
