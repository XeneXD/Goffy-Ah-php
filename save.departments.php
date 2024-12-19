<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['name'])) {
    echo json_encode(['success' => false, 'error' => 'Not authenticated']);
    exit();
}

$deptid = $_POST['deptid'] ?? null;
$deptfullname = $_POST['deptfullname'];
$deptshortname = $_POST['deptshortname'];
$deptcollid = $_POST['deptcollid'];

try {
    $pdo = new PDO("mysql:host=localhost:3306;dbname=usjr", "root", "root");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($deptid) {
        $stmt = $pdo->prepare("
            UPDATE departments
            SET deptfullname = ?, deptshortname = ?, deptcollid = ?
            WHERE deptid = ?
        ");
        $stmt->execute([$deptfullname, $deptshortname, $deptcollid, $deptid]);
    } else {
        $stmt = $pdo->query("SELECT IFNULL(MAX(deptid), 0) AS max_deptid FROM departments");
        $new_deptid = $stmt->fetch(PDO::FETCH_ASSOC)['max_deptid'] + 1;

        $stmt = $pdo->prepare("
            INSERT INTO departments (deptid, deptfullname, deptshortname, deptcollid)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$new_deptid, $deptfullname, $deptshortname, $deptcollid]);
    }

    echo json_encode(['success' => true]);
    exit();
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => htmlspecialchars($e->getMessage())]);
    exit();
}
?>
