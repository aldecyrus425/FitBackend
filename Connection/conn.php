<?php

$serverName = "ANONYMOUS\\SQLEXPRESS";

$connectionOptions = [
    "Database" => "fitclub",
    "Encrypt" => false,
    "TrustServerCertificate" => true
];

$conn = sqlsrv_connect($serverName, $connectionOptions);

if ($conn === false) {
    die(json_encode(sqlsrv_errors()));
}

?>