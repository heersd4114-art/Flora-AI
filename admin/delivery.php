<?php
session_start();
include "../config.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logistics | FloraAI Admin</title>
    <link rel="stylesheet" href="../assests/css/global.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>

<div class="dashboard-container">
    <?php include "includes/sidebar.php"; ?>

    <main class="main-content animate-enter">
        <header style="margin-bottom: 40px;">
            <h1 style="font-size: 2rem; font-weight: 700; color: var(--slate-900);">Delivery</h1>
            <p style="color: var(--slate-400);">Delivery partners and active shipment tracking.</p>
        </header>

        <div class="card-premium">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                <h3 style="color: white; font-size: 1.25rem;">Authorized Partners</h3>
                <a href="add_partner.php" class="btn-primary" style="padding: 10px 20px; font-size: 13px;">
                    <i class='bx bx-user-plus'></i> Register New
                </a>
            </div>

            <table style="width: 100%; border-collapse: collapse;">
                <thead style="background: rgba(255,255,255,0.05); border-bottom: 1px solid rgba(255,255,255,0.1);">
                    <tr>
                        <th style="padding: 20px; text-align: left; font-size: 12px; text-transform: uppercase; color: var(--slate-400);">ID</th>
                        <th style="padding: 20px; text-align: left; font-size: 12px; text-transform: uppercase; color: var(--slate-400);">Partner Identity</th>
                        <th style="padding: 20px; text-align: left; font-size: 12px; text-transform: uppercase; color: var(--slate-400);">Contact</th>
                        <th style="padding: 20px; text-align: left; font-size: 12px; text-transform: uppercase; color: var(--slate-400);">Joined</th>
                        <th style="padding: 20px; text-align: right; font-size: 12px; text-transform: uppercase; color: var(--slate-400);">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $partners_sql = "SELECT * FROM users WHERE role = 'delivery_partner' ORDER BY created_at DESC";
                    $partners = $conn->query($partners_sql);
                    
                    if ($partners->num_rows > 0): 
                        while($row = $partners->fetch_assoc()): 
                    ?>
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.1);">
                        <td style="padding: 20px; color: var(--slate-600);">#<?php echo $row['user_id']; ?></td>
                        <td style="padding: 20px; font-weight: 600;">
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <div style="width: 32px; height: 32px; background: rgba(255,255,255,0.1); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 14px;">
                                    <?php echo strtoupper(substr($row['name'], 0, 1)); ?>
                                </div>
                                <div>
                                    <div style="color: white;"><?php echo $row['name']; ?></div>
                                    <small style="color: var(--slate-400);">Partner</small>
                                </div>
                            </div>
                        </td>
                        <td style="padding: 20px;">
                            <div style="color: var(--slate-400); font-size: 13px;"><?php echo $row['email']; ?></div>
                            <div style="color: var(--slate-600); font-size: 12px;"><?php echo $row['phone']; ?></div>
                        </td>
                        <td style="padding: 20px; color: var(--slate-400);"><?php echo date("M d, Y", strtotime($row['created_at'])); ?></td>
                        <td style="padding: 20px; text-align: right;">
                            <a href="edit_partner.php?id=<?php echo $row['user_id']; ?>" style="color: var(--primary); margin-right: 12px; text-decoration: none; font-weight: 600; font-size: 13px;">Edit</a>
                            <a href="delete_user.php?id=<?php echo $row['user_id']; ?>&redirect=delivery.php" style="color: #ef4444; text-decoration: none; font-weight: 600; font-size: 13px;" onclick="return confirm('Revoke partner access?')">Remove Access</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="5" style="padding: 40px; text-align: center; color: var(--slate-400);">No active delivery partners found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>

</body>
</html>
