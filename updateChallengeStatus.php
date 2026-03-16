<?php
header("Content-Type: application/json");
include "./Connection/conn.php"; // mysqli connection

$data = json_decode(file_get_contents("php://input"), true);

$userId = $data['userId'] ?? '';
$challengeId = $data['challengeId'] ?? '';
$status = $data['status'] ?? ''; // approved, rejected, canceled

if (!$userId || !$challengeId || !$status) {
    echo json_encode(["error" => "Missing parameters"]);
    exit();
}

// Check if record exists
$checkQuery = "SELECT * FROM UserCommunityChallenge WHERE user_id = ? AND challenge_id = ?";
$checkStmt = $conn->prepare($checkQuery);
$checkStmt->bind_param("ss", $userId, $challengeId);
$checkStmt->execute();
$checkResult = $checkStmt->get_result();
$exists = $checkResult->num_rows > 0;
$checkStmt->close();

if ($exists) {
    // Update existing record
    $updateQuery = "UPDATE UserCommunityChallenge
                    SET status = ?
                    WHERE user_id = ? AND challenge_id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("sss", $status, $userId, $challengeId);
} else {
    // Insert new record
    $startedAt = $status === 'approved' ? date('Y-m-d H:i:s') : null;
    $insertQuery = "INSERT INTO UserCommunityChallenge
                    (user_id, challenge_id, status, progress, started_at)
                    VALUES (?, ?, ?, 0, ?)";
    $stmt = $conn->prepare($insertQuery);
    $stmt->bind_param("ssss", $userId, $challengeId, $status, $startedAt);
}

if (!$stmt->execute()) {
    echo json_encode(["error" => "Failed to update challenge status", "details" => $stmt->error]);
    $stmt->close();
    $conn->close();
    exit();
}

echo json_encode([
    "message" => "Challenge status updated",
    "status" => $status
]);

$stmt->close();
$conn->close();
?>