<?php
header("Content-Type: application/json");

include "./Connection/conn.php";

$data = json_decode(file_get_contents("php://input"), true);

// 🔹 Get user ID
$id = trim($data['id'] ?? '');

if (!$id) {
    http_response_code(400);
    echo json_encode(["error" => "User ID is required"]);
    exit();
}

// 🔹 Query user
$query = "SELECT 
    id, 
    name, 
    email, 
    fitness_service, 
    level, 
    age, 
    height, 
    weight, 
    gender
FROM Users WHERE id = ? LIMIT 1";

$stmt = $conn->prepare($query);
$stmt->bind_param("s", $id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 0) {
    http_response_code(404);
    echo json_encode(["error" => "User not found"]);
    $stmt->close();
    $conn->close();
    exit();
}

$user = $result->fetch_assoc();

echo json_encode([
    "user" => [
        "id" => $user['id'],
        "name" => $user['name'],
        "email" => $user['email'],
        "service" => $user['fitness_service'],
        "level" => $user['level'],
        "age" => (int)$user['age'],
        "height" => (float)$user['height'],
        "weight" => (float)$user['weight'],
        "gender" => $user['gender']
    ]
]);

$stmt->close();
$conn->close();
?>