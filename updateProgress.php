<?php
header("Content-Type: application/json");
include "./Connection/conn.php"; // mysqli connection

$data = json_decode(file_get_contents("php://input"), true);

$userId = $data['userId'] ?? '';
$challengeId = $data['challengeId'] ?? '';
$progress = isset($data['progress']) ? floatval($data['progress']) : null;

if (!$userId || !$challengeId || $progress === null) {
    echo json_encode(["error" => "Missing parameters"]);
    exit();
}

$query = "UPDATE UserCommunityChallenge
          SET progress = ?
          WHERE user_id = ? AND challenge_id = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("dss", $progress, $userId, $challengeId);

if (!$stmt->execute()) {
    echo json_encode(["error" => "Failed to update progress", "details" => $stmt->error]);
    $stmt->close();
    $conn->close();
    exit();
}

echo json_encode([
    "message" => "Progress updated"
]);

$stmt->close();
$conn->close();
?>