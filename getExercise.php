<?php
header("Content-Type: application/json");
include "./Connection/conn.php"; // conn.php should use mysqli_connect

$query = "SELECT * FROM Exercises";
$stmt = $conn->prepare($query);

if (!$stmt) {
    http_response_code(500);
    echo json_encode(["error" => "Failed to prepare query", "details" => $conn->error]);
    exit();
}

$stmt->execute();
$result = $stmt->get_result();

$exercises = [];
while ($row = $result->fetch_assoc()) {
    $exercises[] = [
        "id" => $row["id"],
        "name" => $row["name"],
        "description" => $row["description"],
        "videoUrl" => $row["video_url"],
        "category" => $row["category"],
        "duration" => (int)$row["duration"],
        "sets" => (int)$row["sets"],
        "reps" => (int)$row["reps"],
        "difficulty" => $row["difficulty"]
    ];
}

echo json_encode($exercises);

$stmt->close();
$conn->close();
?>