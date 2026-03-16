<?php
header("Content-Type: application/json");
include "./Connection/conn.php";

$data = json_decode(file_get_contents("php://input"), true);
$exerciseId = $data['id'] ?? '';

if (!$exerciseId) {
    http_response_code(400);
    echo json_encode(["error" => "Exercise ID is required"]);
    exit();
}

$deleteQuery = "DELETE FROM Exercises WHERE id = ?";
$deleteStmt = sqlsrv_query($conn, $deleteQuery, [$exerciseId]);

if ($deleteStmt === false) {
    http_response_code(500);
    echo json_encode(["error" => "Failed to delete exercise"]);
    exit();
}

echo json_encode(["message" => "Exercise deleted successfully"]);

sqlsrv_close($conn);
?>