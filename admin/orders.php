<?php
session_start();
include "../config.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit;
}

$sql = "SELECT * FROM orders ORDER BY created_at DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders | FloraAI Admin</title>
    <link rel="stylesheet" href="../assests/css/global.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>

<div class="dashboard-container">
    <?php include "includes/sidebar.php"; ?>

    <main class="main-content animate-enter">
        <header style="margin-bottom: 40px;">
            <h1 style="font-size: 2rem; font-weight: 700; color: var(--slate-900);">Orders</h1>
            <p style="color: var(--slate-400);">Transactions and fulfillment status.</p>
        </header>

        <div class="card-premium" style="padding: 0; overflow: hidden;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead style="background: rgba(255,255,255,0.05); border-bottom: 1px solid rgba(255,255,255,0.1);">
                    <tr>
                        <th style="padding: 20px; text-align: left; font-size: 12px; text-transform: uppercase; color: var(--slate-400);">Order ID</th>
                        <th style="padding: 20px; text-align: left; font-size: 12px; text-transform: uppercase; color: var(--slate-400);">Customer ID</th>
                        <th style="padding: 20px; text-align: left; font-size: 12px; text-transform: uppercase; color: var(--slate-400);">Amount</th>
                        <th style="padding: 20px; text-align: left; font-size: 12px; text-transform: uppercase; color: var(--slate-400);">Date</th>
                        <th style="padding: 20px; text-align: right; font-size: 12px; text-transform: uppercase; color: var(--slate-400);">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.1);">
                            <td style="padding: 20px; font-weight: 600; color: white;">#<?php echo $row['order_id']; ?></td>
                            <td style="padding: 20px; color: var(--slate-600);">User #<?php echo $row['user_id']; ?></td>
                            <td style="padding: 20px; font-weight: 700; color: white;">₹<?php echo number_format($row['total_amount'], 2); ?></td>
                            <td style="padding: 20px; color: var(--slate-400);"><?php echo date("M d, Y", strtotime($row['created_at'])); ?></td>
                            <td style="padding: 20px; text-align: right;">
                                <span style="background: <?php echo $row['status'] == 'Completed' ? 'rgba(34, 197, 94, 0.2)' : 'rgba(234, 88, 12, 0.2)'; ?>; color: <?php echo $row['status'] == 'Completed' ? '#4ade80' : '#fb923c'; ?>; padding: 4px 12px; border-radius: 100px; font-size: 12px; font-weight: 700; text-transform: uppercase;">
                                    <?php echo $row['status']; ?>
                                </span>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="5" style="padding: 40px; text-align: center; color: var(--slate-400);">No orders found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>

</body>
</html>
