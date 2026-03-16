<?php
header("Content-Type: application/json");
include "./Connection/conn.php"; // Make sure conn.php uses mysqli_connect

// Prepare query
$query = "SELECT * FROM Food ORDER BY name ASC";
$result = $conn->query($query);

if (!$result) {
    http_response_code(500);
    echo json_encode(["error" => "Database query failed", "details" => $conn->error]);
    $conn->close();
    exit();
}

$foods = [];

while ($row = $result->fetch_assoc()) {
    $foods[] = [
        "id" => $row["id"],
        "name" => $row["name"],
        "description" => $row["description"],
        "imageUrl" => $row["image_url"],
        "calories" => (float)$row["calories"],
        "protein" => (float)$row["protein"],
        "carbs" => (float)$row["carbs"],
        "fat" => (float)$row["fat"],
        "mealType" => $row["meal_type"],
        "category" => $row["category"],
        "level" => $row["level"]
    ];
}

// Return JSON
echo json_encode($foods);

// Close connection
$conn->close();
?>