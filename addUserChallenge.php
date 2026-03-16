<?php
header("Content-Type: application/json");
include "./Connection/conn.php"; // Make sure conn.php uses mysqli for XAMPP

$data = json_decode(file_get_contents("php://input"), true);

$userId = trim($data['user_id'] ?? '');
$challengeId = trim($data['challenge_id'] ?? '');
$status = trim($data['status'] ?? 'started');
$progress = floatval($data['progress'] ?? 0.0);

if (!$userId || !$challengeId) {
    echo json_encode(["error" => "user_id and challenge_id are required"]);
    exit();
}

// Determine if completed_at should be set
$completedAt = null;
if ($progress >= 1.0 || strtolower($status) === 'completed') {
    $completedAt = date('Y-m-d H:i:s');
}

$startedAt = date('Y-m-d H:i:s');

// Check if user already has this challenge
$checkStmt = $conn->prepare("SELECT * FROM UserCommunityChallenge WHERE user_id = ? AND challenge_id = ?");
$checkStmt->bind_param("ss", $userId, $challengeId);

if (!$checkStmt->execute()) {
    echo json_encode(["error" => "Database error checking existing challenge", "details" => $checkStmt->error]);
    exit();
}

$result = $checkStmt->get_result();

if ($result->num_rows > 0) {
    // Update existing record
    $updateStmt = $conn->prepare("
        UPDATE UserCommunityChallenge
        SET status = ?, progress = ?, started_at = ?, completed_at = ?
        WHERE user_id = ? AND challenge_id = ?
    ");
    $updateStmt->bind_param("sdssss", $status, $progress, $startedAt, $completedAt, $userId, $challengeId);

    if ($updateStmt->execute()) {
        echo json_encode([
            "message" => "User challenge updated successfully",
            "user_id" => $userId,
            "challenge_id" => $challengeId,
            "status" => $status,
            "progress" => $progress,
            "completed_at" => $completedAt
        ]);
    } else {
        echo json_encode(["error" => "Failed to update user challenge", "details" => $updateStmt->error]);
    }

    $updateStmt->close();
} else {
    // Insert new record
    $insertStmt = $conn->prepare("
        INSERT INTO UserCommunityChallenge
        (user_id, challenge_id, status, progress, started_at, completed_at)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    $insertStmt->bind_param("sssdss", $userId, $challengeId, $status, $progress, $startedAt, $completedAt);

    if ($insertStmt->execute()) {
        echo json_encode([
            "message" => "User challenge added successfully",
            "user_id" => $userId,
            "challenge_id" => $challengeId,
            "status" => $status,
            "progress" => $progress,
            "completed_at" => $completedAt
        ]);
    } else {
        echo json_encode(["error" => "Failed to add user challenge", "details" => $insertStmt->error]);
    }

    $insertStmt->close();
}

$checkStmt->close();
$conn->close();
?>