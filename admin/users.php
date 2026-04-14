<?php
session_start();
include "../config.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit;
}

$sql = "SELECT * FROM users WHERE role = 'customer' ORDER BY created_at DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users | FloraAI Admin</title>
    <link rel="stylesheet" href="../assests/css/global.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>

<div class="dashboard-container">
    <?php include "includes/sidebar.php"; ?>

    <main class="main-content animate-enter">
        <header style="margin-bottom: 40px;">
            <h1 style="font-size: 2rem; font-weight: 700; color: var(--slate-900);">Users</h1>
            <p style="color: var(--slate-400);">Manage registered users and access roles.</p>
        </header>

        <div class="card-premium" style="padding: 0; overflow: hidden;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead style="background: rgba(255,255,255,0.05); border-bottom: 1px solid rgba(255,255,255,0.1);">
                    <tr>
                        <th style="padding: 20px; text-align: left; font-size: 12px; text-transform: uppercase; color: var(--slate-400);">ID</th>
                        <th style="padding: 20px; text-align: left; font-size: 12px; text-transform: uppercase; color: var(--slate-400);">User Identity</th>
                        <th style="padding: 20px; text-align: left; font-size: 12px; text-transform: uppercase; color: var(--slate-400);">Role</th>
                        <th style="padding: 20px; text-align: left; font-size: 12px; text-transform: uppercase; color: var(--slate-400);">Contact</th>
                        <th style="padding: 20px; text-align: left; font-size: 12px; text-transform: uppercase; color: var(--slate-400);">Joined</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.1);">
                            <td style="padding: 20px; color: var(--slate-600);">#<?php echo $row['user_id']; ?></td>
                            <td style="padding: 20px; font-weight: 600;">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <div style="width: 32px; height: 32px; background: rgba(255,255,255,0.1); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 14px;">
                                        <?php echo strtoupper(substr($row['name'], 0, 1)); ?>
                                    </div>
                                    <?php echo $row['name']; ?>
                                </div>
                            </td>
                            <td style="padding: 20px;">
                                <span style="background: <?php echo $row['role'] == 'admin' ? 'rgba(255,255,255,0.1)' : 'rgba(255,255,255,0.05)'; ?>; color: <?php echo $row['role'] == 'admin' ? 'white' : '#94a3b8'; ?>; padding: 4px 12px; border-radius: 100px; font-size: 12px; font-weight: 600; text-transform: uppercase;">
                                    <?php echo $row['role']; ?>
                                </span>
                            </td>
                            <td style="padding: 20px; color: var(--slate-600);"><?php echo $row['email']; ?></td>
                            <td style="padding: 20px; color: var(--slate-400);"><?php echo date("M d, Y", strtotime($row['created_at'])); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="5" style="padding: 40px; text-align: center; color: var(--slate-400);">No users found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>

</body>
</html>
