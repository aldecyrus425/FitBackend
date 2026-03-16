<?php
header("Content-Type: application/json");

// MySQL connection settings for XAMPP
$host = "localhost";      // XAMPP default
$user = "root";           // XAMPP default
$password = "";           // XAMPP default
$dbname = "fitclub";      // Your database name

$conn = new mysqli($host, $user, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}

// Connection is successful
// You can now use $conn for queries
?>