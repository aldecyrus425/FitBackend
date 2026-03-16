<?php
header("Content-Type: application/json");
include "./Connection/conn.php";

$raw = file_get_contents("php://input");
$data = json_decode($raw, true);

if (!$data || !isset($data['foodID'])) {
    http_response_code(400);
    echo json_encode(["error" => "Food ID is required"]);
    exit();
}

$foodId = $data['foodID'];

// Delete from database
$deleteQuery = "DELETE FROM Food WHERE id = ?";
$deleteStmt = sqlsrv_query($conn, $deleteQuery, [$foodId]);

if ($deleteStmt === false) {
    http_response_code(500);
    echo json_encode(["error" => "Failed to delete food"]);
    exit();
}


echo json_encode(["message" => "Food deleted successfully"]);

sqlsrv_close($conn);
?>