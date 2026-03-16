<?php
header("Content-Type: application/json");

include "./Connection/conn.php";

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

// Prepare and execute statement
$params = [$user_id, $challenge_id];
$stmt = sqlsrv_query($conn, $query, $params);

if ($stmt === false) {
    $errors = sqlsrv_errors();
    echo json_encode(["error" => "Execution failed", "details" => $errors]);
    exit();
}

// Fetch result
$challenges = [];
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $challenges[] = [
        "challenge_id"   => $row['challenge_id'],
        "title"          => $row['title'],
        "description"    => $row['description'],
        "category"       => $row['category'],
        "level"          => $row['level'],
        "duration_days"  => (int)$row['duration_days'],
        "progress"       => (float)$row['progress'],
        "started_at"     => $row['started_at'] ? $row['started_at']->format('Y-m-d') : null,
        "completed_at"   => $row['completed_at'] ? $row['completed_at']->format('Y-m-d') : null,
        "status"         => $row['status']
    ];
}

echo json_encode($challenges);

// Close connection
sqlsrv_close($conn);
?>