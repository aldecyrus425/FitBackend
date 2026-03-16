<?php
header("Content-Type: application/json");

include "./Connection/conn.php"; // Make sure conn.php uses mysqli

// Read JSON payload from Flutter
$data = json_decode(file_get_contents("php://input"), true);

$category = $data['category'] ?? '';
$level = $data['level'] ?? '';

// Base query
$query = "SELECT * FROM CommunityChallenges WHERE 1=1";
$params = [];
$types = "";

// Add filters if provided
if ($category) {
    $query .= " AND category = ?";
    $params[] = $category;
    $types .= "s";
}

if ($level) {
    $query .= " AND level = ?";
    $params[] = $level;
    $types .= "s";
}

// Prepare statement
$stmt = $conn->prepare($query);
if (!$stmt) {
    http_response_code(500);
    echo json_encode(["error" => "Failed to prepare query", "details" => $conn->error]);
    exit();
}

// Bind parameters dynamically if any
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

// Execute
$stmt->execute();
$result = $stmt->get_result();

$challenges = [];
while ($row = $result->fetch_assoc()) {
    $challenges[] = [
        "id" => $row['id'],
        "title" => $row['title'],
        "description" => $row['description'],
        "category" => $row['category'],
        "level" => $row['level'],
        "durationDays" => (int)$row['duration_days'],
        "progress" => isset($row['progress']) ? (float)$row['progress'] : 0
    ];
}

echo json_encode($challenges);

$stmt->close();
$conn->close();
?>