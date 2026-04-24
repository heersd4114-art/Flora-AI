<?php
session_start();
if (isset($_SESSION['admin_id']) && !isset($_SESSION['user_id'])) {
    header("Location: admin/dashboard.php");
    exit;
}
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_name = $_SESSION['user_name'];
$user_id = $_SESSION['user_id'];

include "config.php";

// Fetch real stats from database
$total_scans = 0;
$healthy_plants = 0;
$issues_found = 0;
$pending_orders = 0;

$stmt = $conn->prepare("SELECT COUNT(*) as total FROM plant_history WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$total_scans = $stmt->get_result()->fetch_assoc()['total'];

$stmt = $conn->prepare("SELECT COUNT(*) as cnt FROM plant_history WHERE user_id = ? AND (disease_detected = 'Healthy' OR disease_detected LIKE '%Healthy%')");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$healthy_plants = $stmt->get_result()->fetch_assoc()['cnt'];

$issues_found = $total_scans - $healthy_plants;

$stmt = $conn->prepare("SELECT COUNT(*) as cnt FROM orders WHERE user_id = ? AND status IN ('placed', 'processing')");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$pending_orders = $stmt->get_result()->fetch_assoc()['cnt'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | FloraAI</title>
    <link rel="stylesheet" href="assests/css/global.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <?php include "includes/header_pwa.php"; ?>
</head>
<body>

<div class="dashboard-container">
    <?php include "dashboard_sidebar.php"; ?>

    <main class="main-content animate-enter">
        <header style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px;">
            <div>
                <h1 style="font-size: 2rem; font-weight: 700; color: var(--slate-900); margin: 0;">Welcome, <?php echo htmlspecialchars($user_name); ?>.</h1>
                <p style="color: var(--slate-400); margin-top: 8px;">Here is your garden's daily intelligence report.</p>
            </div>
            
            <a href="identify.php" class="btn-primary">
                <i class='bx bxs-leaf'></i> Plant Identification
            </a>
        </header>

        <!-- STATS GRID -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 24px; margin-bottom: 40px;">
            <div class="card-premium">
                <div style="font-size: 12px; font-weight: 700; color: var(--slate-400); text-transform: uppercase;">Total Scans</div>
                <div style="font-size: 2.5rem; font-weight: 800; color: var(--slate-900); margin-top: 8px;"><?php echo $total_scans; ?></div>
            </div>
            <div class="card-premium">
                <div style="font-size: 12px; font-weight: 700; color: var(--slate-400); text-transform: uppercase;">Healthy Plants</div>
                <div style="font-size: 2.5rem; font-weight: 800; color: var(--primary); margin-top: 8px;"><?php echo $healthy_plants; ?></div>
            </div>
            <div class="card-premium">
                <div style="font-size: 12px; font-weight: 700; color: var(--slate-400); text-transform: uppercase;">Action Required</div>
                <div style="font-size: 2.5rem; font-weight: 800; color: #ef4444; margin-top: 8px;"><?php echo $issues_found; ?></div>
            </div>
            <div class="card-premium">
                <div style="font-size: 12px; font-weight: 700; color: var(--slate-400); text-transform: uppercase;">Pending Orders</div>
                <div style="font-size: 2.5rem; font-weight: 800; color: var(--accent); margin-top: 8px;"><?php echo $pending_orders; ?></div>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 32px;">
            <div class="card-premium">
                <h3 style="margin-bottom: 24px; font-size: 1.25rem; color: var(--slate-900);">Garden Operations</h3>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <a href="store.php" style="background: #f0fdf4; padding: 24px; border-radius: 12px; text-decoration: none; color: #064e3b !important; transition: 0.2s; border: 1px solid #bbf7d0;">
                        <i class='bx bxs-store' style="font-size: 32px; margin-bottom: 12px; display: block; color: #15803d !important;"></i>
                        <h4 style="margin: 0; color: #064e3b !important;">Marketplace</h4>
                        <p style="margin: 4px 0 0; font-size: 13px; opacity: 0.8; color: #064e3b !important;">Get premium supplies.</p>
                    </a>
                    <a href="disease.php" style="background: #eff6ff; padding: 24px; border-radius: 12px; text-decoration: none; color: #1e3a8a !important; transition: 0.2s; border: 1px solid #bfdbfe;">
                        <i class='bx bxs-report' style="font-size: 32px; margin-bottom: 12px; display: block; color: #1d4ed8 !important;"></i>
                        <h4 style="margin: 0; color: #1e3a8a !important;">Care History</h4>
                        <p style="margin: 4px 0 0; font-size: 13px; opacity: 0.8; color: #1e3a8a !important;">View past diagnostics.</p>
                    </a>
                </div>
            </div>

            <div class="card-premium">
                <h3 style="margin-bottom: 24px; font-size: 1.25rem; color: var(--slate-900);">Recent Activity</h3>
                <div style="display: flex; flex-direction: column; gap: 16px;">
                    <?php
                    $recent = $conn->prepare("SELECT plant_name, disease_detected, scan_date FROM plant_history WHERE user_id = ? ORDER BY scan_date DESC LIMIT 5");
                    $recent->bind_param("i", $user_id);
                    $recent->execute();
                    $recent_result = $recent->get_result();
                    if ($recent_result->num_rows > 0):
                        while ($scan = $recent_result->fetch_assoc()):
                            $is_healthy = (stripos($scan['disease_detected'], 'Healthy') !== false);
                            $dot_color = $is_healthy ? 'var(--primary)' : '#ef4444';
                            $label = $is_healthy ? 'Healthy' : htmlspecialchars($scan['disease_detected']);
                            $time_diff = time() - strtotime($scan['scan_date']);
                            if ($time_diff < 3600) $time_ago = round($time_diff / 60) . 'm ago';
                            elseif ($time_diff < 86400) $time_ago = round($time_diff / 3600) . 'h ago';
                            else $time_ago = round($time_diff / 86400) . 'd ago';
                    ?>
                    <div style="display: flex; align-items: center; gap: 12px; padding-bottom: 12px; border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <div style="width: 8px; height: 8px; background: <?php echo $dot_color; ?>; border-radius: 50%;"></div>
                        <div>
                            <div style="font-weight: 600; font-size: 14px; color: var(--slate-900);"><?php echo htmlspecialchars($scan['plant_name']); ?></div>
                            <div style="font-size: 12px; color: var(--slate-400);"><?php echo $label; ?> • <?php echo $time_ago; ?></div>
                        </div>
                    </div>
                    <?php endwhile; else: ?>
                    <p style="color: var(--slate-400); font-size: 14px;">No scans yet. Start by identifying a plant!</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>
</div>

</body>
</html>
