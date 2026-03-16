<?php

header("Content-Type: application/json");
include "./Connection/conn.php";

$query = "SELECT * FROM Food ORDER BY name ASC";

$stmt = sqlsrv_query($conn, $query);

$foods = [];

while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {

    $foods[] = [
        "id" => $row["id"],
        "name" => $row["name"],
        "description" => $row["description"],
        "imageUrl" => $row["image_url"],
        "calories" => $row["calories"],
        "protein" => $row["protein"],
        "carbs" => $row["carbs"],
        "fat" => $row["fat"],
        "mealType" => $row["meal_type"],
        "category" => $row["category"],
        "level" => $row["level"]
    ];
}

echo json_encode($foods);

sqlsrv_close($conn);

?>