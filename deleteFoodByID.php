<?php
header("Content-Type: application/json");
include "./Connection/conn.php"; // Make sure conn.php uses mysqli

$raw = file_get_contents("php://input");
$data = json_decode($raw, true);

if (!$data || !isset($data['foodID'])) {
    http_response_code(400);
    echo json_encode(["error" => "Food ID is required"]);
    exit();
}

$foodId = $data['foodID'];

// Delete from database using prepared statement
$deleteStmt = $conn->prepare("DELETE FROM Food WHERE id = ?");
$deleteStmt->bind_param("s", $foodId);

if ($deleteStmt->execute()) {
    echo json_encode(["message" => "Food deleted successfully"]);
} else {
    http_response_code(500);
    echo json_encode([
        "error" => "Failed to delete food",
        "details" => $deleteStmt->error
    ]);
}

$deleteStmt->close();
$conn->close();
?>