<?php
header("Content-Type: application/json");

include "./Connection/conn.php";

$query = "SELECT name FROM Levels ORDER BY id";

$stmt = sqlsrv_query($conn, $query);

if ($stmt === false) {
    echo json_encode(["error" => "Failed to fetch levels"]);
    exit();
}

$levels = [];

while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $levels[] = $row['name'];
}

echo json_encode([
    "levels" => $levels
]);

sqlsrv_close($conn);
?>