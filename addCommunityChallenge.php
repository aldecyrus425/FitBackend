<?php
header("Content-Type: application/json");

include "./Connection/conn.php";

$data = json_decode(file_get_contents("php://input"), true);

$title = trim($data['title'] ?? '');
$description = trim($data['description'] ?? '');
$category = trim($data['category'] ?? '');
$level = trim($data['level'] ?? '');
$durationDays = intval($data['durationDays'] ?? 0);
$timeNotify = trim($data['notifyTime'] ?? null); 

if (!$title || !$durationDays) {
    echo json_encode([
        "error" => "Title and duration are required"
    ]);
    exit();
}

$id = uniqid(); 

$stmt = $conn->prepare("INSERT INTO CommunityChallenges 
    (id, title, description, time_notify, category, level, duration_days)
    VALUES (?, ?, ?, ?, ?, ?, ?)");

if (!$stmt) {
    echo json_encode([
        "error" => "Failed to prepare statement: " . $conn->error
    ]);
    exit();
}

$stmt->bind_param(
    "ssssssi", 
    $id, 
    $title, 
    $description, 
    $timeNotify,  
    $category, 
    $level, 
    $durationDays
);

if ($stmt->execute()) {
    echo json_encode([
        "message" => "Community challenge added successfully",
        "challenge_id" => $id
    ]);
} else {
    echo json_encode([
        "error" => "Failed to add community challenge: " . $stmt->error
    ]);
}

$stmt->close();
$conn->close();
?>