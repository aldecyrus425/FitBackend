<?php
header("Content-Type: application/json");

include "./Connection/conn.php"; // Make sure conn.php uses mysqli

$query = "SELECT * FROM CommunityChallenges ORDER BY created_at DESC";

$result = $conn->query($query);

if (!$result) {
    http_response_code(500);
    echo json_encode(["error" => "Database query failed", "details" => $conn->error]);
    exit();
}

$challenges = [];

while ($row = $result->fetch_assoc()) {
    $challenges[] = [
        "id" => $row["id"],
        "title" => $row["title"],
        "description" => $row["description"],
        "category" => $row["category"],
        "level" => $row["level"],
        "durationDays" => (int)$row["duration_days"],
    ];
}

echo json_encode($challenges);

$result->free();
$conn->close();
?>