<?php
header("Content-Type: application/json");

include "./Connection/conn.php";

$data = json_decode(file_get_contents("php://input"), true);

// 🔹 Get Data
$name = trim($data['name'] ?? '');
$email = trim($data['email'] ?? '');
$password = trim($data['password'] ?? '');
$fitnessService = trim($data['fitnessService'] ?? '');
$level = trim($data['level'] ?? '');
$age = intval($data['age'] ?? 0);
$height = floatval($data['height'] ?? 0);
$weight = floatval($data['weight'] ?? 0);
$gender = trim($data['gender'] ?? '');

// 🔹 Validation
if (!$name || !$email || !$password) {
    http_response_code(400);
    echo json_encode(["error" => "Name, email, and password are required"]);
    exit();
}

// 🔹 Check existing email
$checkQuery = "SELECT id FROM Users WHERE email = ? LIMIT 1";
$stmt = $conn->prepare($checkQuery);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    http_response_code(409);
    echo json_encode(["error" => "Email already registered"]);
    $stmt->close();
    $conn->close();
    exit();
}

$stmt->close();

// 🔹 Insert user
$userId = uniqid();
$passwordHash = password_hash($password, PASSWORD_BCRYPT);

$insertQuery = "INSERT INTO Users 
(id, name, email, password_hash, fitness_service, level, age, height, weight, gender, is_admin)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0)";

$insertStmt = $conn->prepare($insertQuery);
$insertStmt->bind_param(
    "ssssssidds",
    $userId,
    $name,
    $email,
    $passwordHash,
    $fitnessService,
    $level,
    $age,
    $height,
    $weight,
    $gender
);

if (!$insertStmt->execute()) {
    http_response_code(500);
    echo json_encode([
        "error" => "Failed to register user",
        "details" => $insertStmt->error
    ]);
    $insertStmt->close();
    $conn->close();
    exit();
}

http_response_code(201);

echo json_encode([
    "message" => "User registered successfully",
    "user" => [
        "id" => $userId,
        "name" => $name,
        "email" => $email,
        "fitnessService" => $fitnessService,
        "level" => $level,
        "age" => $age,
        "height" => $height,
        "weight" => $weight
    ]
]);

$insertStmt->close();
$conn->close();
?>