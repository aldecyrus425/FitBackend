<?php
header("Content-Type: application/json");
include "./Connection/conn.php"; // Make sure conn.php uses mysqli_connect

$foodId = $_GET['id'] ?? '';

if (!$foodId) {
    http_response_code(400);
    echo json_encode(["error" => "Food ID is required"]);
    $conn->close();
    exit();
}

// Prepare statement to prevent SQL injection
$stmt = $conn->prepare("SELECT * FROM Food WHERE id = ?");
$stmt->bind_param("s", $foodId);

if (!$stmt->execute()) {
    http_response_code(500);
    echo json_encode(["error" => "Database query failed", "details" => $stmt->error]);
    $stmt->close();
    $conn->close();
    exit();
}

$result = $stmt->get_result();
$food = $result->fetch_assoc();

if (!$food) {
    http_response_code(404);
    echo json_encode(["error" => "Food not found"]);
    $stmt->close();
    $conn->close();
    exit();
}

echo json_encode([
    "id" => $food["id"],
    "name" => $food["name"],
    "description" => $food["description"],
    "imageUrl" => $food["image_url"],
    "calories" => (float)$food["calories"],
    "protein" => (float)$food["protein"],
    "carbs" => (float)$food["carbs"],
    "fat" => (float)$food["fat"],
    "mealType" => $food["meal_type"],
    "category" => $food["category"],
    "level" => $food["level"]
]);

$stmt->close();
$conn->close();
?>