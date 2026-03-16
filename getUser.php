<?php
header("Content-Type: application/json");

include "./Connection/conn.php"; // Make sure this uses mysqli_connect

$query = "SELECT * FROM Users WHERE is_admin = 0";

$stmt = $conn->prepare($query);

if (!$stmt->execute()) {
    echo json_encode(["error" => "Failed to fetch users", "details" => $stmt->error]);
    $stmt->close();
    $conn->close();
    exit();
}

$result = $stmt->get_result();
$users = [];

while ($row = $result->fetch_assoc()) {
    $users[] = [
        "id" => $row['id'],
        "name" => $row['name'],
        "email" => $row['email'],
        "category" => $row['fitness_service'] ?? '-',
        "level" => $row['level'] ?? '-',
    ];
}

echo json_encode([
    "users" => $users
]);

$stmt->close();
$conn->close();
?>