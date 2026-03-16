<?php
header("Content-Type: application/json");

include "./Connection/conn.php";

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
    SELECT uc.challenge_id, uc.status, uc.progress, uc.started_at, uc.completed_at
    FROM UserCommunityChallenge uc
    WHERE uc.user_id = ?
";

$params = [$userId];
$stmt = sqlsrv_query($conn, $query, $params);

if ($stmt === false) {
    echo json_encode([
        "error" => "Failed to fetch user challenges"
    ]);
    exit();
}

$userChallenges = [];
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $userChallenges[] = [
        "challenge_id" => $row['challenge_id'],
        "status" => $row['status'],
        "progress" => floatval($row['progress']),
        "started_at" => $row['started_at'] ? $row['started_at']->format('Y-m-d H:i:s') : null,
        "completed_at" => $row['completed_at'] ? $row['completed_at']->format('Y-m-d H:i:s') : null,
    ];
}

echo json_encode($userChallenges);

sqlsrv_close($conn);
?>