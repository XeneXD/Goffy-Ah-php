<?php
session_start();
header('Content-Type: application/json');
$response = ['success' => false];

if (!isset($_SESSION['name'])) {
    $response['error'] = 'Not authenticated';
    echo json_encode($response);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $collid = $data['id'];

    try {
        $pdo = new PDO("mysql:host=localhost:3306;dbname=usjr", "root", "root");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $pdo->prepare("SELECT * FROM colleges WHERE collid = ?");
        $stmt->execute([$collid]);
        $college = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$college) {
            $response['error'] = 'College not found';
            echo json_encode($response);
            exit();
        }

        $stmt = $pdo->prepare("DELETE FROM colleges WHERE collid = ?");
        $stmt->execute([$collid]);

        if ($stmt->rowCount() > 0) {
            $response['success'] = true;
        } else {
            $response['error'] = 'Failed to delete college';
        }
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        $response['error'] = 'An error occurred while deleting the college';
    }
} else {
    $response['error'] = 'Invalid request';
}

echo json_encode($response);
