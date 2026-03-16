<?php
header("Content-Type: application/json");
include "./Connection/conn.php";

$query = "SELECT * FROM Exercises";
$stmt = sqlsrv_query($conn, $query);

if ($stmt === false) {
    http_response_code(500);
    echo json_encode(["error" => "Database query failed"]);
    exit();
}

$exercises = [];
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $exercises[] = [
        "id" => $row["id"],
        "name" => $row["name"],
        "description" => $row["description"],
        "videoUrl" => $row["video_url"],
        "category" => $row["category"],
        "duration" => (int)$row["duration"],
        "sets" => (int)$row["sets"],
        "reps" => (int)$row["reps"],
        "difficulty" => $row["difficulty"]
    ];
}

echo json_encode($exercises);
sqlsrv_close($conn);
?>