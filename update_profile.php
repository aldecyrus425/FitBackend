<?php
header("Content-Type: application/json");

include "./Connection/conn.php";

$data = json_decode(file_get_contents("php://input"), true);

// 🔹 Get Data
$id = trim($data['id'] ?? '');
$name = trim($data['fullname'] ?? '');
$email = trim($data['email'] ?? '');
$fitnessService = trim($data['service'] ?? '');
$level = trim($data['level'] ?? '');
$age = intval($data['age'] ?? 0);
$height = floatval($data['height'] ?? 0);
$weight = floatval($data['weight'] ?? 0);
$gender = trim($data['gender'] ?? '');

// 🔹 Validation
if (!$id || !$name || !$email) {
    http_response_code(400);
    echo json_encode(["error" => "ID, name, and email are required"]);
    exit();
}

// 🔹 Check if email belongs to another user
$checkQuery = "SELECT id FROM Users WHERE email = ? AND id != ? LIMIT 1";
$stmt = $conn->prepare($checkQuery);
$stmt->bind_param("ss", $email, $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    http_response_code(409);
    echo json_encode(["error" => "Email already in use by another account"]);
    $stmt->close();
    $conn->close();
    exit();
}

$stmt->close();

$query = "UPDATE Users SET 
        name=?, 
        email=?, 
        fitness_service=?, 
        level=?, 
        age=?, 
        height=?, 
        weight=? ,
        gender=?
        WHERE id=?";

    $stmt = $conn->prepare($query);
    $stmt->bind_param(
        "ssssiddss",
        $name,
        $email,
        $fitnessService,
        $level,
        $age,
        $height,
        $weight,
        $gender,
        $id
    );

// 🔹 Execute
if (!$stmt->execute()) {
    http_response_code(500);
    echo json_encode([
        "error" => "Failed to update profile",
        "details" => $stmt->error
    ]);
    $stmt->close();
    $conn->close();
    exit();
}

echo json_encode([
    "message" => "Profile updated successfully"
]);

$stmt->close();
$conn->close();
?>