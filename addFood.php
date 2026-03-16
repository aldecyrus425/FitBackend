<?php
header("Content-Type: application/json");

include "./Connection/conn.php"; // conn.php should use mysqli

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["error" => "Invalid request method"]);
    exit();
}

$name = $_POST['name'] ?? '';
$description = $_POST['description'] ?? '';
$calories = floatval($_POST['calories'] ?? 0);
$protein = floatval($_POST['protein'] ?? 0);
$carbs = floatval($_POST['carbs'] ?? 0);
$fat = floatval($_POST['fat'] ?? 0);
$mealType = $_POST['mealType'] ?? '';
$category = $_POST['category'] ?? '';
$level = $_POST['level'] ?? '';

if (!$name || !$description) {
    echo json_encode(["error" => "Missing required fields"]);
    exit();
}

if (!isset($_FILES['image'])) {
    echo json_encode(["error" => "Image is required"]);
    exit();
}

$image = $_FILES['image'];

$uploadDir = "uploads/foods/";

if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

$imageName = uniqid() . "_" . basename($image['name']);
$imagePath = $uploadDir . $imageName;

if (!move_uploaded_file($image['tmp_name'], $imagePath)) {
    echo json_encode(["error" => "Failed to upload image"]);
    exit();
}

$foodId = uniqid();

// Prepare MySQL statement
$stmt = $conn->prepare("INSERT INTO Food 
    (id, name, description, image_url, calories, protein, carbs, fat, meal_type, category, level)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

if (!$stmt) {
    echo json_encode(["error" => "Failed to prepare statement: " . $conn->error]);
    exit();
}

$stmt->bind_param(
    "ssssddddsss",
    $foodId,
    $name,
    $description,
    $imagePath,
    $calories,
    $protein,
    $carbs,
    $fat,
    $mealType,
    $category,
    $level
);

if ($stmt->execute()) {
    echo json_encode([
        "message" => "Food added successfully",
        "image_url" => $imagePath
    ]);
} else {
    echo json_encode(["error" => "Failed to insert food: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>