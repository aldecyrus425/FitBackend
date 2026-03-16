<?php
header("Content-Type: application/json");
include "./Connection/conn.php"; // Make sure conn.php uses mysqli

$data = json_decode(file_get_contents("php://input"), true);
$exerciseId = $data['id'] ?? '';

if (!$exerciseId) {
    http_response_code(400);
    echo json_encode(["error" => "Exercise ID is required"]);
    exit();
}

// Delete the exercise using mysqli prepared statement
$deleteStmt = $conn->prepare("DELETE FROM Exercises WHERE id = ?");
$deleteStmt->bind_param("s", $exerciseId);

if ($deleteStmt->execute()) {
    echo json_encode(["message" => "Exercise deleted successfully"]);
} else {
    http_response_code(500);
    echo json_encode([
        "error" => "Failed to delete exercise",
        "details" => $deleteStmt->error
    ]);
}

$deleteStmt->close();
$conn->close();
?>