<?php
header("Content-Type: application/json");

include "./Connection/conn.php"; // Make sure this uses mysqli_connect

$userId = $_GET['user_id'] ?? '';

if (!$userId) {
    http_response_code(400);
    echo json_encode(["error" => "user_id is required"]);
    exit();
}

$query = "
SELECT 
    uc.user_id,
    uc.challenge_id,
    uc.status,
    uc.progress,
    uc.started_at,
    c.title,
    c.description,
    c.category,
    c.level,
    c.duration_days,
    c.time_notify  -- include the notification time
FROM UserCommunityChallenge uc
INNER JOIN CommunityChallenges c 
    ON uc.challenge_id = c.id
WHERE 
    uc.user_id = ?
    AND uc.status NOT IN ('completed','cancelled')
    AND DATE_ADD(uc.started_at, INTERVAL c.duration_days DAY) >= NOW()
ORDER BY uc.started_at DESC
";

$stmt = $conn->prepare($query);
$stmt->bind_param("s", $userId);

if (!$stmt->execute()) {
    http_response_code(500);
    echo json_encode(["error" => "Database query failed", "details" => $stmt->error]);
    $stmt->close();
    $conn->close();
    exit();
}

$result = $stmt->get_result();
$challenges = [];

while ($row = $result->fetch_assoc()) {
    $challenges[] = [
        "challenge_id" => $row['challenge_id'],
        "title" => $row['title'],
        "description" => $row['description'],
        "category" => $row['category'],
        "level" => $row['level'],
        "duration_days" => (int)$row['duration_days'],
        "progress" => (float)$row['progress'],
        "started_at" => date('Y-m-d', strtotime($row['started_at'])),
        "status" => $row['status'],
        "time_notify" => $row['time_notify'] // send time_notify to client
    ];
}

echo json_encode($challenges);

$stmt->close();
$conn->close();
?>