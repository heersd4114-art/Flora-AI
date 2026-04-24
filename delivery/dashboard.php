<?php
session_start();
include "../config.php";

if (!isset($_SESSION['partner_id'])) {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['partner_id'];

// Get partner details
$partner_stmt = $conn->prepare("SELECT * FROM delivery_partners WHERE user_id = ?");
$partner_stmt->bind_param("i", $user_id);
$partner_stmt->execute();
$partner = $partner_stmt->get_result()->fetch_assoc();

if (!$partner) {
    // Auto-create partner profile if missing (Self-Healing)
    $stmt = $conn->prepare("INSERT INTO delivery_partners (user_id, vehicle_type, vehicle_number, current_status) VALUES (?, 'Standard', 'N/A', 'Available')");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    
    // Retry fetch
    $partner_stmt->execute();
    $partner = $partner_stmt->get_result()->fetch_assoc();
}

$partner_id = $partner['partner_id'];

// Get assigned shipments with item details (Grouped by order)
$ship_sql = "SELECT s.*, o.shipping_address, o.total_amount, u.name as customer_name, u.phone as customer_phone 
             FROM shipments s 
             JOIN orders o ON s.order_id = o.order_id 
             JOIN users u ON o.user_id = u.user_id
             WHERE s.partner_id = ? 
             ORDER BY s.assigned_at DESC";
$ship_stmt = $conn->prepare($ship_sql);
$ship_stmt->bind_param("i", $partner_id);
$ship_stmt->execute();
$shipments_result = $ship_stmt->get_result();

$shipments = [];
while ($row = $shipments_result->fetch_assoc()) {
    $order_id = $row['order_id'];
    // Fetch items for this order
    $item_stmt = $conn->prepare("SELECT oi.*, p.name as product_name, p.image_url FROM order_items oi JOIN products p ON oi.product_id = p.product_id WHERE oi.order_id = ?");
    $item_stmt->bind_param("i", $order_id);
    $item_stmt->execute();
    $items = [];
    $item_res = $item_stmt->get_result();
    while ($item = $item_res->fetch_assoc()) {
        $items[] = $item;
    }
    $row['items'] = $items;
    $shipments[] = $row;
}
?>
<link rel="stylesheet" href="../assests/css/global.css">
<style>
    body { background: var(--bg-main); }
    .header-delivery {
        background: var(--primary-gradient);
        color: white;
        padding: 40px 24px 80px;
        text-align: center;
        position: relative;
    }
    .container-delivery {
        max-width: 800px;
        margin: -50px auto 40px;
        padding: 0 24px;
        position: relative;
        z-index: 10;
    }
    .shipment-glass-card {
        background: #1e293b;
        border-radius: var(--radius-lg);
        padding: 32px;
        margin-bottom: 24px;
        border: 1px solid rgba(255,255,255,0.1);
        box-shadow: 0 20px 40px -10px rgba(0,0,0,0.3);
        transition: var(--transition-smooth);
    }
    .shipment-glass-card:hover { transform: translateY(-4px); }
    .card-top { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 16px; }
    .order-id { font-weight: 800; font-size: 1.1rem; color: white; }
    .status-pill { padding: 6px 16px; border-radius: var(--radius-full); font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; }
    
    .status-pending { background: rgba(239, 68, 68, 0.2); color: #f87171; }
    .status-pickedup { background: rgba(59, 130, 246, 0.2); color: #60a5fa; }
    .status-transit { background: rgba(245, 158, 11, 0.2); color: #fbbf24; }
    .status-outfordelivery { background: rgba(34, 197, 94, 0.2); color: #4ade80; }
    .status-delivered { background: rgba(34, 197, 94, 0.1); color: #22c55e; }

    .customer-info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; }
    .info-item { display: flex; flex-direction: column; gap: 4px; }
    .info-label { font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; }
    .info-value { font-weight: 600; color: white; }
    
    .delivery-actions { margin-top: 32px; display: flex; justify-content: flex-end; }
    .btn-delivery {
        background: var(--primary-gradient);
        color: white;
        padding: 12px 24px;
        border-radius: var(--radius-full);
        font-weight: 700;
        border: none;
        cursor: pointer;
        transition: var(--transition-smooth);
        box-shadow: 0 10px 20px -5px rgba(46, 125, 50, 0.3);
    }
    .btn-delivery:hover { transform: translateY(-2px); box-shadow: 0 15px 30px -5px rgba(46, 125, 50, 0.4); }
</style>

<div class="header-delivery">
    <div class="container-premium animate-fade">
        <h1 style="font-size: 2.5rem; margin-bottom: 8px;">Delivery Partner Dashboard</h1>
        <p style="opacity: 0.9; font-size: 1.1rem;">
            <i class='bx bxs-truck'></i> Logged in as: <strong><?php echo htmlspecialchars($partner['name']); ?></strong> 
            <span style="margin: 0 12px; opacity: 0.5;">|</span>
            <a href="../logout.php" style="color: white; text-decoration: underline;">Secure Logout</a>
        </p>
    </div>
</div>

<div class="container-delivery animate-fade">
    <h3 class="premium-section-title" style="margin-bottom: 24px;">Active Shipments</h3>
    <?php if (count($shipments) > 0): ?>
        <?php foreach($shipments as $s): ?>
            <div class="shipment-glass-card">
                <div class="card-top">
                    <span class="order-id">ORDER #<?php echo $s['order_id']; ?></span>
                    <span class="status-pill status-<?php echo strtolower(str_replace(' ', '', $s['status'])); ?>">
                        <?php echo $s['status']; ?>
                    </span>
                </div>
                
                <div class="customer-info-grid">
                    <div class="info-item">
                        <span class="info-label">Customer</span>
                        <span class="info-value"><?php echo htmlspecialchars($s['customer_name']); ?> (<?php echo htmlspecialchars($s['customer_phone']); ?>)</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Total Payment</span>
                        <span class="info-value">₹<?php echo $s['total_amount']; ?></span>
                    </div>
                    <div class="info-item" style="grid-column: span 2;">
                        <span class="info-label">Package Destination</span>
                        <span class="info-value"><?php echo htmlspecialchars($s['shipping_address']); ?></span>
                    </div>
                </div>

                <div style="margin-top: 24px; padding: 16px; background: rgba(0,0,0,0.2); border-radius: 12px; border: 1px solid rgba(255,255,255,0.05);">
                    <span class="info-label" style="display: block; margin-bottom: 12px;">Package Contents (Product IDs)</span>
                    <?php foreach($s['items'] as $item): ?>
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px; border-bottom: 1px solid rgba(255,255,255,0.03); padding-bottom: 8px;">
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <img src="../<?php echo $item['image_url']; ?>" style="width: 40px; height: 40px; border-radius: 8px; object-fit: cover;">
                                <div>
                                    <div style="color: white; font-weight: 700; font-size: 14px;"><?php echo htmlspecialchars($item['product_name']); ?></div>
                                    <div style="color: #94a3b8; font-size: 11px;">ID: #FLORA-<?php echo $item['product_id']; ?></div>
                                </div>
                            </div>
                            <div style="color: var(--primary); font-weight: 800;">x<?php echo $item['quantity']; ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="delivery-actions">
                    <?php if ($s['status'] == 'Pending'): ?>
                        <button onclick="updateStatus(<?php echo $s['shipment_id']; ?>, 'Picked Up')" class="btn-delivery">Confirm Pick Up</button>
                    <?php elseif ($s['status'] == 'Picked Up'): ?>
                        <button onclick="updateStatus(<?php echo $s['shipment_id']; ?>, 'In Transit')" class="btn-delivery">Mark as In Transit</button>
                    <?php elseif ($s['status'] == 'In Transit'): ?>
                        <button onclick="updateStatus(<?php echo $s['shipment_id']; ?>, 'Out for Delivery')" class="btn-delivery">Set Out for Delivery</button>
                    <?php elseif ($s['status'] == 'Out for Delivery'): ?>
                        <button onclick="updateStatus(<?php echo $s['shipment_id']; ?>, 'Delivered')" class="btn-delivery">Confirm Final Delivery</button>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="glass" style="padding: 60px; text-align: center; border-radius: var(--radius-lg);">
            <i class='bx bx-check-double' style="font-size: 48px; color: var(--primary); margin-bottom: 16px;"></i>
            <h3>All Caught Up!</h3>
            <p>No new shipments assigned to you at the moment.</p>
        </div>
    <?php endif; ?>
</div>

<script>
function updateStatus(shipmentId, status) {
    if(confirm("Transition order to " + status + "?")) {
        fetch('update_status.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'shipment_id=' + shipmentId + '&status=' + status
        })
        .then(res => res.text())
        .then(data => {
            alert(data);
            location.reload();
        });
    }
}
</script>

