<?php
header("Content-Type: application/json");

$host = "localhost";     
$user = "root";         
$password = "";           
$dbname = "fitclub";    

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}

?>