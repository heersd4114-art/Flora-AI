<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
include "../config.php";

$sql = "SELECT o.*, u.name as user_name FROM orders o JOIN users u ON o.user_id = u.user_id ORDER BY o.created_at DESC";
$result = $conn->query($sql);

$orders = [];
while ($row = $result->fetch_assoc()) {
    $orders[] = $row;
}

echo json_encode(["status" => "success", "data" => $orders]);
?>
