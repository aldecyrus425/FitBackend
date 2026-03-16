<?php
header("Content-Type: application/json");

include "./Connection/conn.php"; // should use mysqli_connect

// Get parameters
$user_id = $_GET['user_id'] ?? '';
$challenge_id = $_GET['challenge_id'] ?? '';

// Validate input
if (empty($user_id) || empty($challenge_id)) {
    echo json_encode([]);
    exit();
}

// Prepare query
$query = "
    SELECT uc.user_id, uc.challenge_id, uc.status, uc.progress, uc.started_at, uc.completed_at,
           c.title, c.description, c.category, c.level, c.duration_days
    FROM UserCommunityChallenge uc
    INNER JOIN CommunityChallenges c ON uc.challenge_id = c.id
    WHERE uc.user_id = ? AND uc.challenge_id = ?
";

$stmt = $conn->prepare($query);
$stmt->bind_param("ss", $user_id, $challenge_id);

if (!$stmt->execute()) {
    echo json_encode(["error" => "Execution failed", "details" => $stmt->error]);
    $stmt->close();
    $conn->close();
    exit();
}

$result = $stmt->get_result();
$challenges = [];

while ($row = $result->fetch_assoc()) {
    $challenges[] = [
        "challenge_id"   => $row['challenge_id'],
        "title"          => $row['title'],
        "description"    => $row['description'],
        "category"       => $row['category'],
        "level"          => $row['level'],
        "duration_days"  => (int)$row['duration_days'],
        "progress"       => (float)$row['progress'],
        "started_at"     => $row['started_at'] ? date('Y-m-d', strtotime($row['started_at'])) : null,
        "completed_at"   => $row['completed_at'] ? date('Y-m-d', strtotime($row['completed_at'])) : null,
        "status"         => $row['status']
    ];
}

echo json_encode($challenges);

$stmt->close();
$conn->close();
?>