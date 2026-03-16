<?php
header("Content-Type: application/json");

include "./Connection/conn.php";

$input = json_decode(file_get_contents("php://input"), true);

if (!isset($input['user_id']) || empty($input['user_id'])) {
    echo json_encode(["error" => "Missing user_id"]);
    exit();
}

$user_id = $input['user_id'];

$query = "DELETE FROM Users WHERE id = ?";
$params = [$user_id];

$stmt = sqlsrv_query($conn, $query, $params);

if ($stmt === false) {
    echo json_encode(["error" => "Failed to delete user"]);
    exit();
}

$rowsAffected = sqlsrv_rows_affected($stmt);
if ($rowsAffected === false || $rowsAffected === 0) {
    echo json_encode(["error" => "User not found or cannot delete admin"]);
    exit();
}

echo json_encode([
    "message" => "User deleted successfully"
]);

sqlsrv_close($conn);
?>