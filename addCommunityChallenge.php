<?php

header("Content-Type: application/json");

include "./Connection/conn.php";

$data = json_decode(file_get_contents("php://input"), true);

$title = trim($data['title'] ?? '');
$description = trim($data['description'] ?? '');
$category = trim($data['category'] ?? '');
$level = trim($data['level'] ?? '');
$durationDays = intval($data['durationDays'] ?? 0);

if (!$title || !$durationDays) {
    echo json_encode([
        "error" => "Title and duration are required"
    ]);
    exit();
}

$id = uniqid();

$query = "INSERT INTO CommunityChallenges 
(id, title, description, category, level, duration_days)
VALUES (?, ?, ?, ?, ?, ?)";

$params = [
    $id,
    $title,
    $description,
    $category,
    $level,
    $durationDays
];

$stmt = sqlsrv_query($conn, $query, $params);

if ($stmt === false) {
    echo json_encode([
        "error" => "Failed to add community challenge"
    ]);
    exit();
}

echo json_encode([
    "message" => "Community challenge added successfully",
    "challenge_id" => $id
]);

sqlsrv_close($conn);

?>