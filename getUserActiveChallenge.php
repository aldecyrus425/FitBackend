<?php
header("Content-Type: application/json");

include "./Connection/conn.php";

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
    c.duration_days
FROM UserCommunityChallenge uc
INNER JOIN CommunityChallenges c 
    ON uc.challenge_id = c.id
WHERE 
    uc.user_id = ?
    AND uc.status NOT IN ('completed','canceled')
    AND DATEADD(day, c.duration_days, uc.started_at) >= GETDATE()
ORDER BY uc.started_at DESC
";

$params = [$userId];

$stmt = sqlsrv_query($conn, $query, $params);

if ($stmt === false) {
    http_response_code(500);
    echo json_encode(["error" => "Database query failed"]);
    exit();
}

$challenges = [];

while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {

    $challenges[] = [
        "challenge_id" => $row['challenge_id'],
        "title" => $row['title'],
        "description" => $row['description'],
        "category" => $row['category'],
        "level" => $row['level'],
        "duration_days" => $row['duration_days'],
        "progress" => $row['progress'],
        "started_at" => $row['started_at']->format('Y-m-d'),
        "status" => $row['status']
    ];
}

echo json_encode($challenges);

sqlsrv_close($conn);
?>