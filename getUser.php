<?php
header("Content-Type: application/json");

include "./Connection/conn.php";

$query = "SELECT * FROM Users WHERE is_admin = 0";

$stmt = sqlsrv_query($conn, $query);

if ($stmt === false) {
    echo json_encode(["error" => "Failed to fetch users"]);
    exit();
}

$users = [];

while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $users[] = [
        "id" => $row['id'],
        "name" => $row['name'],
        "email" => $row['email'],
        "category" => $row['fitness_service'] ?? '-',
        "level" => $row['level'] ?? '-',
    ];
}

echo json_encode([
    "users" => $users
]);

sqlsrv_close($conn);
?>