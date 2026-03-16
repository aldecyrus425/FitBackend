<?php
header("Content-Type: application/json");

include "./Connection/conn.php"; // should use mysqli_connect

$data = json_decode(file_get_contents("php://input"), true);

// You can also support GET parameter for convenience
$userId = trim($_GET['user_id'] ?? ($data['user_id'] ?? ''));

if (!$userId) {
    echo json_encode([
        "error" => "user_id is required"
    ]);
    exit();
}

// Query to get all challenges for this user
$query = "
    SELECT challenge_id, status, progress, started_at, completed_at
    FROM UserCommunityChallenge
    WHERE user_id = ?
";

$stmt = $conn->prepare($query);
$stmt->bind_param("s", $userId);

if (!$stmt->execute()) {
    echo json_encode([
        "error" => "Failed to fetch user challenges",
        "details" => $stmt->error
    ]);
    $stmt->close();
    $conn->close();
    exit();
}

$result = $stmt->get_result();

$userChallenges = [];
while ($row = $result->fetch_assoc()) {
    $userChallenges[] = [
        "challenge_id" => $row['challenge_id'],
        "status" => $row['status'],
        "progress" => floatval($row['progress']),
        "started_at" => $row['started_at'] ? date('Y-m-d H:i:s', strtotime($row['started_at'])) : null,
        "completed_at" => $row['completed_at'] ? date('Y-m-d H:i:s', strtotime($row['completed_at'])) : null,
    ];
}

echo json_encode($userChallenges);

$stmt->close();
$conn->close();
?>