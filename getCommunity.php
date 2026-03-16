<?php
header("Content-Type: application/json");

include "./Connection/conn.php";

// Fetch all challenges
$query = "SELECT * FROM CommunityChallenges ORDER BY created_at DESC";
$stmt = sqlsrv_query($conn, $query);

if ($stmt === false) {
    http_response_code(500);
    echo json_encode(["error" => "Database query failed"]);
    exit();
}

$challenges = [];
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $challenges[] = [
        "id" => $row["id"],
        "title" => $row["title"],
        "description" => $row["description"],
        "category" => $row["category"],
        "level" => $row["level"],
        "durationDays" => (int)$row["duration_days"],
    ];
}

// Return JSON response
echo json_encode($challenges);

sqlsrv_close($conn);
?>