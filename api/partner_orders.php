<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
include "../config.php";

// Fetch only orders assigned to this partner through the shipments table
$partner_id = $_POST['partner_id'] ?? null;

if (!$partner_id) {
    echo json_encode(["status" => "error", "message" => "Partner ID required"]);
    exit;
}

// 1. Get the local partner_id from the user_id (mobile sends user_id)
$p_stmt = $conn->prepare("SELECT partner_id FROM delivery_partners WHERE user_id = ?");
$p_stmt->bind_param("i", $partner_id);
$p_stmt->execute();
$p_res = $p_stmt->get_result()->fetch_assoc();
$local_id = $p_res['partner_id'] ?? 0;

if (!$local_id) {
    // Auto-create partner profile if missing (matches web dashboard self-healing)
    $u_stmt = $conn->prepare("SELECT name, phone FROM users WHERE user_id = ? AND role = 'delivery_partner'");
    $u_stmt->bind_param("i", $partner_id);
    $u_stmt->execute();
    $u_res = $u_stmt->get_result()->fetch_assoc();
    if ($u_res) {
        $ins = $conn->prepare("INSERT INTO delivery_partners (user_id, name, contact_number, vehicle_type, status) VALUES (?, ?, ?, 'Bike', 'Available')");
        $ins->bind_param("iss", $partner_id, $u_res['name'], $u_res['phone']);
        $ins->execute();
        $local_id = $conn->insert_id;
    } else {
        echo json_encode(["status" => "success", "data" => []]); exit;
    }
}

$sql = "SELECT s.*, o.shipping_address as address, o.total_amount, u.name as customer_name, u.phone as customer_phone 
        FROM shipments s 
        JOIN orders o ON s.order_id = o.order_id 
        JOIN users u ON o.user_id = u.user_id
        WHERE s.partner_id = ? AND s.status != 'Delivered'
        ORDER BY s.assigned_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $local_id);
$stmt->execute();
$result = $stmt->get_result();

$deliveries = [];
while ($row = $result->fetch_assoc()) {
    $order_id = $row['order_id'];
    
    // Fetch items for this specific order
    $item_sql = "SELECT oi.quantity, p.name, p.product_id, p.image_url 
                 FROM order_items oi 
                 JOIN products p ON oi.product_id = p.product_id 
                 WHERE oi.order_id = ?";
    $i_stmt = $conn->prepare($item_sql);
    $i_stmt->bind_param("i", $order_id);
    $i_stmt->execute();
    $i_res = $i_stmt->get_result();
    
    $items = [];
    while($item = $i_res->fetch_assoc()) {
        $items[] = $item;
    }
    
    $row['items'] = $items;
    $deliveries[] = $row;
}

echo json_encode(["status" => "success", "data" => $deliveries]);
?>
