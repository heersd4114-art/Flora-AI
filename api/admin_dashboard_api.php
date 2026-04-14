<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
include "../config.php";

$stats = [
    'total_users' => 0,
    'total_orders' => 0,
    'total_revenue' => 0.0
];

// Get total users excluding admins
$resU = $conn->query("SELECT COUNT(*) as c FROM users WHERE role != 'admin'");
if ($resU) $stats['total_users'] = $resU->fetch_assoc()['c'];

// Get total orders
$resO = $conn->query("SELECT COUNT(*) as c FROM orders");
if ($resO) $stats['total_orders'] = $resO->fetch_assoc()['c'];

// Get total revenue
$resR = $conn->query("SELECT SUM(total_price) as sum FROM orders WHERE status = 'Delivered'");
if ($resR) $stats['total_revenue'] = round($resR->fetch_assoc()['sum'] ?? 0.0, 2);

echo json_encode(["status" => "success", "data" => $stats]);
?>
