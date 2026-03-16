<?php
header("Content-Type: application/json");

include "./Connection/conn.php"; // Make sure conn.php uses mysqli

// Read JSON payload
$data = json_decode(file_get_contents("php://input"), true);
$challengeId = $data['id'] ?? '';

if (!$challengeId) {
    http_response_code(400);
    echo json_encode(["error" => "Challenge ID is required"]);
    exit();
}

// Delete the challenge using mysqli prepared statement
$deleteStmt = $conn->prepare("DELETE FROM CommunityChallenges WHERE id = ?");
$deleteStmt->bind_param("s", $challengeId);

if ($deleteStmt->execute()) {
    echo json_encode(["message" => "Challenge deleted successfully"]);
} else {
    http_response_code(500);
    echo json_encode([
        "error" => "Failed to delete challenge",
        "details" => $deleteStmt->error
    ]);
}

$deleteStmt->close();
$conn->close();
?>