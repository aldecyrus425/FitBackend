<?php
header("Content-Type: application/json");
include "./Connection/conn.php";

$exerciseId = $_GET['id'] ?? '';

if (!$exerciseId) {
    http_response_code(400);
    echo json_encode(["error" => "Exercise ID is required"]);
    exit();
}

$query = "SELECT * FROM Exercises WHERE id = ?";
$stmt = sqlsrv_query($conn, $query, [$exerciseId]);

if ($stmt === false) {
    http_response_code(500);
    echo json_encode(["error" => "Database query failed"]);
    exit();
}

$exercise = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

if (!$exercise) {
    http_response_code(404);
    echo json_encode(["error" => "Exercise not found"]);
    exit();
}

echo json_encode([
    "id" => $exercise["id"],
    "name" => $exercise["name"],
    "description" => $exercise["description"],
    "videoUrl" => $exercise["video_url"],
    "category" => $exercise["category"],
    "duration" => (int)$exercise["duration"],
    "sets" => (int)$exercise["sets"],
    "reps" => (int)$exercise["reps"],
    "difficulty" => $exercise["difficulty"]
]);

sqlsrv_close($conn);
?>