<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $collegeId = $input['college_id'] ?? null;

    if ($collegeId) {
        try {
            $pdo = new PDO("mysql:host=localhost:3306;dbname=usjr", "root", "root");
            $stmt = $pdo->prepare("SELECT progid, progfullname FROM programs WHERE progcollid = ?");
            $stmt->execute([$collegeId]);
            $programs = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode(['programs' => $programs]);
        } catch (PDOException $e) {
            echo json_encode(['error' => htmlspecialchars($e->getMessage())]);
        }
    } else {
        echo json_encode(['error' => 'Invalid college ID']);
    }
} else {
    echo json_encode(['error' => 'Invalid request method']);
}
?>
