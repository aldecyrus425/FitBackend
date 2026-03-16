<?php
header("Content-Type: application/json");

include "./Connection/conn.php";

$query = "SELECT name FROM Services ORDER BY id";

$stmt = sqlsrv_query($conn, $query);

if ($stmt === false) {
    echo json_encode(["error" => "Failed to fetch services"]);
    exit();
}

$services = [];

while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $services[] = $row['name'];
}

echo json_encode([
    "services" => $services
]);

sqlsrv_close($conn);
?>