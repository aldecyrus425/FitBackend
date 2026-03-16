<?php
header("Content-Type: application/json");

include "./Connection/conn.php"; // Make sure conn.php uses mysqli now

// Get JSON input
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

$id = uniqid(); // unique ID for challenge

// Prepare SQL statement for MySQL
$stmt = $conn->prepare("INSERT INTO CommunityChallenges 
    (id, title, description, category, level, duration_days)
    VALUES (?, ?, ?, ?, ?, ?)");

if (!$stmt) {
    echo json_encode([
        "error" => "Failed to prepare statement: " . $conn->error
    ]);
    exit();
}

// Bind parameters
$stmt->bind_param(
    "sssssi", 
    $id, 
    $title, 
    $description, 
    $category, 
    $level, 
    $durationDays
);

// Execute statement
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