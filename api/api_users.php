<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
include "../config.php";

$sql = "SELECT user_id, name, email, role, created_at FROM users WHERE role = 'customer' ORDER BY created_at DESC";
$result = $conn->query($sql);

$users = [];
while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}

echo json_encode(["status" => "success", "data" => $users]);
?>
