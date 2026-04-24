<?php
session_start();
include "../config.php";

if (!isset($_SESSION['partner_id'])) {
    die("Unauthorized");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $shipment_id = $_POST['shipment_id'];
    $status = $_POST['status'];

    // Update shipment status
    $stmt = $conn->prepare("UPDATE shipments SET status = ? WHERE shipment_id = ?");
    $stmt->bind_param("si", $status, $shipment_id);
    
    if ($stmt->execute()) {
        // Find the order ID for this shipment
        $res = $conn->query("SELECT order_id FROM shipments WHERE shipment_id = $shipment_id");
        $order = $res->fetch_assoc();
        
        if ($order) {
            $order_id = $order['order_id'];
            
            // Map shipment status to order status
            $order_status = "";
            if ($status == "Picked Up" || $status == "In Transit") {
                $order_status = "Shipped";
            } elseif ($status == "Out for Delivery") {
                $order_status = "Processing"; // Or stay Shipped
            } elseif ($status == "Delivered") {
                $order_status = "Delivered";
                $conn->query("UPDATE shipments SET delivered_at = CURRENT_TIMESTAMP WHERE shipment_id = $shipment_id");
            }

            if ($order_status != "") {
                $stmt_order = $conn->prepare("UPDATE orders SET status = ? WHERE order_id = ?");
                $stmt_order->bind_param("si", $order_status, $order_id);
                $stmt_order->execute();
            }
        }
        echo "Status updated successfully!";
    } else {
        echo "Error updating status: " . $conn->error;
    }
} else {
    echo "Invalid request";
}
