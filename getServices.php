<?php
header("Content-Type: application/json");

include "./Connection/conn.php"; // Make sure this uses mysqli_connect

$query = "SELECT name FROM Services ORDER BY id";

$stmt = $conn->prepare($query);

if (!$stmt->execute()) {
    echo json_encode(["error" => "Failed to fetch services", "details" => $stmt->error]);
    $stmt->close();
    $conn->close();
    exit();
}

$result = $stmt->get_result();
$services = [];

while ($row = $result->fetch_assoc()) {
    $services[] = $row['name'];
}

echo json_encode([
    "services" => $services
]);

$stmt->close();
$conn->close();
?>