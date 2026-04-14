<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");

include "../config.php";

$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['user_id'])) {
    $user_id = $data['user_id'];

    $stmt = $conn->prepare("SELECT order_id, total_amount, status, created_at, payment_method FROM orders WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $orders = [];
    while ($row = $result->fetch_assoc()) {
        $oid = $row['order_id'];
        $item_stmt = $conn->prepare("SELECT oi.*, p.name, p.image_url FROM order_items oi JOIN products p ON oi.product_id = p.product_id WHERE oi.order_id = ?");
        $item_stmt->bind_param("i", $oid);
        $item_stmt->execute();
        $item_res = $item_stmt->get_result();
        
        $items = [];
        while ($item = $item_res->fetch_assoc()) {
            $item['image_url'] = str_replace('../', '', $item['image_url']);
            $items[] = $item;
        }
        $row['items'] = $items;
        $orders[] = $row;
    }

    echo json_encode(["status" => "success", "data" => $orders]);
} else {
    echo json_encode(["status" => "error", "message" => "User ID required"]);
}
?>
