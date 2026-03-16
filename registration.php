<?php
header("Content-Type: application/json");

include "./Connection/conn.php";

$data = json_decode(file_get_contents("php://input"), true);

$name = trim($data['name'] ?? '');
$email = trim($data['email'] ?? '');
$password = trim($data['password'] ?? '');
$fitnessService = trim($data['fitnessService'] ?? '');
$level = trim($data['level'] ?? '');

if (!$name || !$email || !$password) {
    http_response_code(400);
    echo json_encode(["error" => "Name, email, and password are required"]);
    exit();
}

$checkQuery = "SELECT id FROM Users WHERE email = ?";
$params = [$email];

$stmt = sqlsrv_query($conn, $checkQuery, $params);

if ($stmt === false) {
    echo json_encode(["error" => "Database query failed"]);
    exit();
}

if (sqlsrv_has_rows($stmt)) {
    http_response_code(409);
    echo json_encode(["error" => "Email already registered"]);
    exit();
}

$userId = uniqid();
$passwordHash = password_hash($password, PASSWORD_BCRYPT);

$insertQuery = "INSERT INTO Users (id, name, email, password_hash, fitness_service, level, is_admin)
                VALUES (?, ?, ?, ?, ?, ?, 0)";

$params = [$userId, $name, $email, $passwordHash, $fitnessService, $level];

$insertStmt = sqlsrv_query($conn, $insertQuery, $params);

if ($insertStmt === false) {
    http_response_code(500);
    echo json_encode(["error" => "Failed to register user"]);
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
        "level" => $level
    ]
]);

sqlsrv_close($conn);
?>