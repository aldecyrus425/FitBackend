<?php
header("Content-Type: application/json");

include "./Connection/conn.php";

$data = json_decode(file_get_contents("php://input"), true);

$email = trim($data['email'] ?? '');
$password = trim($data['password'] ?? '');

if (!$email || !$password) {
    http_response_code(400);
    echo json_encode(["error" => "Email and password are required"]);
    exit();
}

$query = "SELECT id, name, email, password_hash, fitness_service, level, is_admin 
          FROM Users 
          WHERE email = ?";

$params = [$email];

$stmt = sqlsrv_query($conn, $query, $params);

if ($stmt === false) {
    http_response_code(500);
    echo json_encode(["error" => "Database query failed"]);
    exit();
}

$user = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

if (!$user) {
    http_response_code(401);
    echo json_encode(["error" => "Invalid email or password"]);
    exit();
}

if (!password_verify($password, $user['password_hash'])) {
    http_response_code(401);
    echo json_encode(["error" => "Invalid email or password"]);
    exit();
}

unset($user['password_hash']);

echo json_encode([
    "message" => "Login successful",
    "user" => $user
]);

sqlsrv_close($conn);
?>