<?php
header("Content-Type: application/json");
include "./Connection/conn.php"; // conn.php should use mysqli_connect

$exerciseId = $_GET['id'] ?? '';

if (!$exerciseId) {
    http_response_code(400);
    echo json_encode(["error" => "Exercise ID is required"]);
    exit();
}

// Prepare query
$query = "SELECT * FROM Exercises WHERE id = ?";
$stmt = $conn->prepare($query);

if (!$stmt) {
    http_response_code(500);
    echo json_encode(["error" => "Failed to prepare query", "details" => $conn->error]);
    exit();
}

// Bind parameter and execute
$stmt->bind_param("s", $exerciseId);
$stmt->execute();
$result = $stmt->get_result();
$exercise = $result->fetch_assoc();

if (!$exercise) {
    http_response_code(404);
    echo json_encode(["error" => "Exercise not found"]);
    $stmt->close();
    $conn->close();
    exit();
}

// Return JSON
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

$stmt->close();
$conn->close();
?>