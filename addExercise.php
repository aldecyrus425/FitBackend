<?php

header("Content-Type: application/json");

include "./Connection/conn.php";

$data = json_decode(file_get_contents("php://input"), true);

$name = trim($data['name'] ?? '');
$description = trim($data['description'] ?? '');
$videoUrl = trim($data['videoUrl'] ?? '');
$category = trim($data['category'] ?? '');
$duration = intval($data['duration'] ?? 0);
$sets = intval($data['sets'] ?? 1);
$reps = intval($data['reps'] ?? 0);
$difficulty = trim($data['difficulty'] ?? '');

if (!$name || !$duration) {
    echo json_encode([
        "error" => "Exercise name and duration are required"
    ]);
    exit();
}

$id = uniqid();

$query = "INSERT INTO Exercises 
(id, name, description, video_url, category, duration, sets, reps, difficulty)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

$params = [
    $id,
    $name,
    $description,
    $videoUrl,
    $category,
    $duration,
    $sets,
    $reps,
    $difficulty
];

$stmt = sqlsrv_query($conn, $query, $params);

if ($stmt === false) {
    echo json_encode([
        "error" => "Failed to add exercise"
    ]);
    exit();
}

echo json_encode([
    "message" => "Exercise added successfully",
    "exercise_id" => $id
]);

sqlsrv_close($conn);

?>