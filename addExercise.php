<?php
header("Content-Type: application/json");

include "./Connection/conn.php"; // Make sure conn.php uses mysqli

// Get JSON input
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

// Prepare SQL statement for MySQL
$stmt = $conn->prepare("INSERT INTO Exercises 
    (id, name, description, video_url, category, duration, sets, reps, difficulty)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

if (!$stmt) {
    echo json_encode([
        "error" => "Failed to prepare statement: " . $conn->error
    ]);
    exit();
}

// Bind parameters
$stmt->bind_param(
    "sssssiiss", 
    $id, 
    $name, 
    $description, 
    $videoUrl, 
    $category, 
    $duration, 
    $sets, 
    $reps, 
    $difficulty
);

// Execute statement
if ($stmt->execute()) {
    echo json_encode([
        "message" => "Exercise added successfully",
        "exercise_id" => $id
    ]);
} else {
    echo json_encode([
        "error" => "Failed to add exercise: " . $stmt->error
    ]);
}

$stmt->close();
$conn->close();
?>