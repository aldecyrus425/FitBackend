<?php

header("Content-Type: application/json");
include "./Connection/conn.php";

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
$checkQuery = "SELECT * FROM UserCommunityChallenge WHERE user_id = ? AND challenge_id = ?";
$checkStmt = sqlsrv_query($conn, $checkQuery, [$userId, $challengeId]);

if ($checkStmt === false) {
    echo json_encode(["error" => "Database error checking existing challenge"]);
    exit();
}

if (sqlsrv_has_rows($checkStmt)) {
    // Update existing record
    $updateQuery = "UPDATE UserCommunityChallenge
        SET status = ?, progress = ?, started_at = ?, completed_at = ?
        WHERE user_id = ? AND challenge_id = ?";

    $params = [$status, $progress, $startedAt, $completedAt, $userId, $challengeId];
    $stmt = sqlsrv_query($conn, $updateQuery, $params);

    if ($stmt === false) {
        echo json_encode(["error" => "Failed to update user challenge"]);
        exit();
    }

    echo json_encode([
        "message" => "User challenge updated successfully",
        "user_id" => $userId,
        "challenge_id" => $challengeId,
        "status" => $status,
        "progress" => $progress,
        "completed_at" => $completedAt
    ]);
} else {
    // Insert new record
    $insertQuery = "INSERT INTO UserCommunityChallenge
        (user_id, challenge_id, status, progress, started_at, completed_at)
        VALUES (?, ?, ?, ?, ?, ?)";

    $params = [$userId, $challengeId, $status, $progress, $startedAt, $completedAt];
    $stmt = sqlsrv_query($conn, $insertQuery, $params);

    if ($stmt === false) {
        echo json_encode(["error" => "Failed to add user challenge"]);
        exit();
    }

    echo json_encode([
        "message" => "User challenge added successfully",
        "user_id" => $userId,
        "challenge_id" => $challengeId,
        "status" => $status,
        "progress" => $progress,
        "completed_at" => $completedAt
    ]);
}

sqlsrv_close($conn);
?>