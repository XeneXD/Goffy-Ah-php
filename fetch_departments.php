<?php
header('Content-Type: application/json');
$method = $_SERVER['REQUEST_METHOD'];

try {
    $pdo = new PDO("mysql:host=localhost:3306;dbname=usjr", "root", "root");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($method === 'GET') {
        if (isset($_GET['collid'])) {
            $collegeId = $_GET['collid'];
            $stmt = $pdo->prepare("SELECT deptid, deptfullname FROM departments WHERE deptcollid = ?");
            $stmt->execute([$collegeId]);
            $departments = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['success' => true, 'departments' => $departments]);
        } else {
            $stmt = $pdo->query("SELECT collid, collfullname FROM colleges");
            $colleges = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['success' => true, 'colleges' => $colleges]);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    }
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'An error occurred while processing the request.']);
}
?>