<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
include "../config.php";

$sql = "SELECT user_id, name, email, role, created_at FROM users WHERE role = 'delivery_partner' OR role = 'partner' ORDER BY created_at DESC";
$result = $conn->query($sql);

$partners = [];
while ($row = $result->fetch_assoc()) {
    $partners[] = $row;
}

echo json_encode(["status" => "success", "data" => $partners]);
?>
