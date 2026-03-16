<?php
header("Content-Type: application/json");
include "./Connection/conn.php";

$foodId = $_GET['id'] ?? '';

if (!$foodId) {
    http_response_code(400);
    echo json_encode(["error" => "Food ID is required"]);
    exit();
}

$query = "SELECT * FROM Food WHERE id = ?";
$params = [$foodId];

$stmt = sqlsrv_query($conn, $query, $params);

if ($stmt === false) {
    http_response_code(500);
    echo json_encode(["error" => "Database query failed"]);
    exit();
}

$food = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

if (!$food) {
    http_response_code(404);
    echo json_encode(["error" => "Food not found"]);
    exit();
}

echo json_encode([
    "id" => $food["id"],
    "name" => $food["name"],
    "description" => $food["description"],
    "imageUrl" => $food["image_url"],
    "calories" => $food["calories"],
    "protein" => $food["protein"],
    "carbs" => $food["carbs"],
    "fat" => $food["fat"],
    "mealType" => $food["meal_type"],
    "category" => $food["category"],
    "level" => $food["level"]
]);

sqlsrv_close($conn);
?>