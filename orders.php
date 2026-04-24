<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
include "config.php";

$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM orders WHERE user_id = $user_id ORDER BY created_at DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders | FloraAI</title>
    <link rel="stylesheet" href="assests/css/global.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        .timeline { display: flex; justify-content: space-between; position: relative; margin: 30px 0 20px; }
        .timeline::before { content: ''; position: absolute; top: 12px; left: 10%; width: 80%; height: 4px; background: #334155; z-index: 1; border-radius: 2px; }
        .timeline-step { position: relative; z-index: 2; text-align: center; width: 25%; }
        .timeline-bullet { width: 28px; height: 28px; background: #334155; color: transparent; border-radius: 50%; margin: 0 auto 8px; border: 4px solid #1e293b; transition: all 0.3s; display: flex; align-items: center; justify-content: center; font-size: 14px; }
        .timeline-step.active .timeline-bullet { background: var(--primary); color: white; box-shadow: 0 0 10px rgba(34,197,94,0.5); }
        .timeline-label { font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; }
        .timeline-step.active .timeline-label { color: white; }
    </style>
</head>
<body>

<div class="dashboard-container">
    <?php include "dashboard_sidebar.php"; ?>

    <main class="main-content animate-enter">
        <header style="margin-bottom: 40px;">
            <h1 style="font-size: 2rem; font-weight: 700; color: var(--slate-900);">My Orders</h1>
            <p style="color: var(--slate-400);">Tracking information for previous purchases.</p>
        </header>

        <div style="display: flex; flex-direction: column; gap: 24px;">
            <?php if ($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                <div class="card-premium" style="display: flex; flex-direction: column; gap: 16px; border-left: 6px solid var(--primary);">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <div style="font-weight: 800; font-size: 1.1rem; color: var(--slate-900); margin-bottom: 4px;">Order #<?php echo $row['order_id']; ?></div>
                            <div style="font-size: 13px; color: var(--slate-600);">
                                <?php echo date('F d, Y', strtotime($row['created_at'])); ?> • <?php echo $row['payment_method']; ?>
                            </div>
                        </div>
                        
                        <div style="text-align: right;">
                            <span style="display: block; font-size: 11px; text-transform: uppercase; font-weight: 700; color: var(--slate-400); margin-bottom: 4px;">Total</span>
                            <span style="font-size: 1.5rem; font-weight: 700; color: var(--slate-900);">₹<?php echo number_format($row['total_amount'], 2); ?></span>
                        </div>
                    </div>
                    
                    <?php
                    $status = strtolower($row['status']);
                    $s1 = 'active';
                    $s2 = ($status == 'processing' || $status == 'shipped' || $status == 'delivered') ? 'active' : '';
                    $s3 = ($status == 'shipped' || $status == 'delivered') ? 'active' : '';
                    $s4 = ($status == 'delivered') ? 'active' : '';
                    ?>
                    <div class="timeline">
                        <div class="timeline-step <?php echo $s1; ?>"><div class="timeline-bullet"><i class='bx bx-check'></i></div><div class="timeline-label">Placed</div></div>
                        <div class="timeline-step <?php echo $s2; ?>"><div class="timeline-bullet"><i class='bx bx-check'></i></div><div class="timeline-label">Processing</div></div>
                        <div class="timeline-step <?php echo $s3; ?>"><div class="timeline-bullet"><i class='bx bx-check'></i></div><div class="timeline-label">Shipped</div></div>
                        <div class="timeline-step <?php echo $s4; ?>"><div class="timeline-bullet"><i class='bx bx-check'></i></div><div class="timeline-label">Delivered</div></div>
                    </div>
                    </div>
                    
                    <div style="border-top: 1px solid #f1f5f9; padding-top: 16px;">
                        <?php 
                        $oid = $row['order_id'];
                        $items = $conn->query("SELECT oi.*, p.name, p.image_url FROM order_items oi JOIN products p ON oi.product_id = p.product_id WHERE oi.order_id = $oid");
                        while($item = $items->fetch_assoc()):
                        ?>
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <img src="<?php echo $item['image_url']; ?>" style="width: 32px; height: 32px; border-radius: 4px; object-fit: cover;">
                                <span style="font-size: 14px; color: var(--slate-700);"><?php echo $item['name']; ?> <span style="color: var(--slate-400);">x<?php echo $item['quantity']; ?></span></span>
                            </div>
                            <a href="add_to_cart.php?id=<?php echo $item['product_id']; ?>&redirect=cart" style="color: var(--primary); font-size: 1.2rem;"><i class='bx bx-cart-add'></i></a>
                        </div>
                        <?php endwhile; ?>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No orders found.</p>
            <?php endif; ?>
        </div>
    </main>
</div>

</body>
</html>
