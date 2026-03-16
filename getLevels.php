<?php
header("Content-Type: application/json");

include "./Connection/conn.php"; // Make sure this uses mysqli_connect

$query = "SELECT name FROM Levels ORDER BY id";

$stmt = $conn->prepare($query);

if (!$stmt->execute()) {
    echo json_encode(["error" => "Failed to fetch levels", "details" => $stmt->error]);
    $stmt->close();
    $conn->close();
    exit();
}

$result = $stmt->get_result();
$levels = [];

while ($row = $result->fetch_assoc()) {
    $levels[] = $row['name'];
}

echo json_encode([
    "levels" => $levels
]);

$stmt->close();
$conn->close();
?>