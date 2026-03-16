<?php

header("Content-Type: application/json");
include "./Connection/conn.php";

$data = json_decode(file_get_contents("php://input"), true);

$userId = $data['userId'];
$challengeId = $data['challengeId'];
$progress = $data['progress'];

$query = "UPDATE UserCommunityChallenge
          SET progress = ?
          WHERE user_id = ? AND challenge_id = ?";

$params = [$progress, $userId, $challengeId];

$stmt = sqlsrv_query($conn, $query, $params);

echo json_encode([
    "message" => "Progress updated"
]);

?>