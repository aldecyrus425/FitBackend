<?php
header("Content-Type: application/json");
include "./Connection/conn.php";

// Read JSON payload from Flutter
$data = json_decode(file_get_contents("php://input"), true);

$category = $data['category'] ?? '';
$level = $data['level'] ?? '';

// Base query
$query = "SELECT * FROM CommunityChallenges WHERE 1=1";
$params = [];

// Add filters if provided
if ($category) {
    $query .= " AND category = ?";
    $params[] = $category;
}

if ($level) {
    $query .= " AND level = ?";
    $params[] = $level;
}

$stmt = sqlsrv_query($conn, $query, $params);

if ($stmt === false) {
    http_response_code(500);
    echo json_encode(["error" => "Failed to fetch community challenges"]);
    exit();
}

$challenges = [];
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $challenges[] = [
        "id" => $row['id'],
        "title" => $row['title'],
        "description" => $row['description'],
        "category" => $row['category'],
        "level" => $row['level'],
        "durationDays" => (int)$row['duration_days'],
        "progress" => (float)$row['progress']
    ];
}

echo json_encode($challenges);
sqlsrv_close($conn);
?>