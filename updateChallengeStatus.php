<?php

header("Content-Type: application/json");
include "./Connection/conn.php";

$data = json_decode(file_get_contents("php://input"), true);

$userId = $data['userId'] ?? '';
$challengeId = $data['challengeId'] ?? '';
$status = $data['status'] ?? ''; // approved, rejected, canceled

if (!$userId || !$challengeId || !$status) {
    echo json_encode(["error" => "Missing parameters"]);
    exit();
}

$checkQuery = "SELECT * FROM UserCommunityChallenge
               WHERE user_id = ? AND challenge_id = ?";

$checkParams = [$userId, $challengeId];
$checkStmt = sqlsrv_query($conn, $checkQuery, $checkParams);

if (sqlsrv_fetch_array($checkStmt, SQLSRV_FETCH_ASSOC)) {

    $query = "UPDATE UserCommunityChallenge
              SET status = ?
              WHERE user_id = ? AND challenge_id = ?";

    $params = [$status, $userId, $challengeId];

} else {

    $query = "INSERT INTO UserCommunityChallenge
              (user_id, challenge_id, status, progress, started_at)
              VALUES (?, ?, ?, 0,
                      CASE WHEN ? = 'approved' THEN GETDATE() ELSE NULL END)";

    $params = [$userId, $challengeId, $status, $status];
}

$stmt = sqlsrv_query($conn, $query, $params);

if ($stmt === false) {
    echo json_encode(["error" => "Failed to update challenge status"]);
    exit();
}

echo json_encode([
    "message" => "Challenge status updated",
    "status" => $status
]);

sqlsrv_close($conn);

?>