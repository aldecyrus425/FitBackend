<?php
header("Content-Type: application/json");
include "./Connection/conn.php"; // Make sure conn.php uses mysqli

$input = json_decode(file_get_contents("php://input"), true);

if (!isset($input['user_id']) || empty($input['user_id'])) {
    echo json_encode(["error" => "Missing user_id"]);
    exit();
}

$user_id = $input['user_id'];

// Delete from database using prepared statement
$stmt = $conn->prepare("DELETE FROM Users WHERE id = ?");
$stmt->bind_param("s", $user_id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode(["message" => "User deleted successfully"]);
    } else {
        echo json_encode(["error" => "User not found or cannot delete admin"]);
    }
} else {
    http_response_code(500);
    echo json_encode(["error" => "Failed to delete user", "details" => $stmt->error]);
}

$stmt->close();
$conn->close();
?>