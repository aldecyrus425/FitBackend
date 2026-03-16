<?php
header("Content-Type: application/json");

include "./Connection/conn.php";

// Read JSON payload
$data = json_decode(file_get_contents("php://input"), true);
$challengeId = $data['id'] ?? '';

if (!$challengeId) {
    http_response_code(400);
    echo json_encode(["error" => "Challenge ID is required"]);
    exit();
}

// Delete the challenge
$deleteQuery = "DELETE FROM CommunityChallenges WHERE id = ?";
$deleteStmt = sqlsrv_query($conn, $deleteQuery, [$challengeId]);

if ($deleteStmt === false) {
    http_response_code(500);
    echo json_encode(["error" => "Failed to delete challenge"]);
    exit();
}

// Success response
echo json_encode(["message" => "Challenge deleted successfully"]);

sqlsrv_close($conn);
?>