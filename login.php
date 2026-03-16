<?php
header("Content-Type: application/json");

include "./Connection/conn.php"; // should use mysqli_connect

$data = json_decode(file_get_contents("php://input"), true);

$email = trim($data['email'] ?? '');
$password = trim($data['password'] ?? '');

if (!$email || !$password) {
    http_response_code(400);
    echo json_encode(["error" => "Email and password are required"]);
    exit();
}

// Prepare statement
$query = "SELECT id, name, email, password_hash, fitness_service, level, is_admin 
          FROM Users 
          WHERE email = ? 
          LIMIT 1";

$stmt = $conn->prepare($query);
$stmt->bind_param("s", $email);

if (!$stmt->execute()) {
    http_response_code(500);
    echo json_encode(["error" => "Database query failed", "details" => $stmt->error]);
    $stmt->close();
    $conn->close();
    exit();
}

$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    http_response_code(401);
    echo json_encode(["error" => "Invalid email or password"]);
    $stmt->close();
    $conn->close();
    exit();
}

if (!password_verify($password, $user['password_hash'])) {
    http_response_code(401);
    echo json_encode(["error" => "Invalid email or password"]);
    $stmt->close();
    $conn->close();
    exit();
}

// Remove password hash before returning
unset($user['password_hash']);

echo json_encode([
    "message" => "Login successful",
    "user" => $user
]);

$stmt->close();
$conn->close();
?>