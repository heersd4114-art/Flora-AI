<?php
session_start();
include "../config.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit;
}

// Get Stats
$userCount = $conn->query("SELECT COUNT(*) as count FROM users WHERE role='customer'")->fetch_assoc()['count'];
$productCount = $conn->query("SELECT COUNT(*) as count FROM products")->fetch_assoc()['count'];
$orderCount = $conn->query("SELECT COUNT(*) as count FROM orders WHERE status='Pending'")->fetch_assoc()['count'];
?>
<link rel="stylesheet" href="../assests/css/global.css">
<style>
    body { background: var(--bg-main); overflow-x: hidden; }
    .hub-content { animation: fadeIn 0.8s ease-out; }
    .intelligence-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 32px;
        margin-bottom: 48px;
    }
    .stat-glass-admin {
        background: white;
        padding: 32px;
        border-radius: var(--radius-lg);
        display: flex;
        align-items: center;
        gap: 24px;
        box-shadow: 0 20px 40px -15px rgba(0,0,0,0.05);
        border: 1px solid var(--glass-border);
        transition: var(--transition-smooth);
    }
    .stat-glass-admin:hover { transform: translateY(-8px); box-shadow: 0 40px 60px -20px rgba(0,0,0,0.1); }
    .icon-admin {
        width: 72px; height: 72px;
        border-radius: 20px;
        display: flex; align-items: center; justify-content: center;
        font-size: 32px;
        box-shadow: 0 12px 24px -6px rgba(0,0,0,0.1);
    }
    .blue-admin { background: #eff6ff; color: #2563eb; }
    .green-admin { background: #f0fdf4; color: #16a34a; }
    .orange-admin { background: #fff7ed; color: #ea580c; }
    
    .val-admin { font-size: 2.25rem; font-weight: 800; color: var(--text-main); margin: 0; }
    .lab-admin { color: var(--text-muted); font-size: 14px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; }

    .platform-visuals {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 32px;
    }

    .glass-panel-heavy {
        background: white;
        border-radius: var(--radius-lg);
        padding: 40px;
        border: 1px solid var(--glass-border);
    }

    .admin-section-title {
        font-size: 1.5rem;
        margin-bottom: 32px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .mock-chart {
        width: 100%;
        height: 300px;
        background: linear-gradient(rgba(241, 245, 249, 0.5), rgba(241, 245, 249, 0.2));
        border-radius: var(--radius-md);
        display: flex;
        align-items: flex-end;
        gap: 12px;
        padding: 24px;
        border: 1px dashed #e2e8f0;
    }
    .bar { flex: 1; background: var(--primary-gradient); border-radius: 4px 4px 0 0; min-height: 20%; animation: grow 1.5s ease-out forwards; }
    @keyframes grow { from { height: 0; } to { height: var(--h); } }
</style>

<div class="dashboard-container">
    <?php include "includes/sidebar.php"; ?>

    <main class="main-content animate-enter">
        <header style="margin-bottom: 40px;">
            <h1 style="font-size: 2rem; font-weight: 700; color: var(--slate-900);">Dashboard</h1>
            <p style="color: var(--slate-400);">System-wide performance metrics.</p>
        </header>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 24px; margin-bottom: 40px;">
            <div class="card-premium">
                <div style="font-size: 12px; font-weight: 700; color: var(--slate-400); text-transform: uppercase;">Total Users</div>
                <div style="font-size: 2.5rem; font-weight: 800; color: white; margin-top: 8px;"><?php echo $userCount; ?></div>
            </div>
            <div class="card-premium">
                <div style="font-size: 12px; font-weight: 700; color: var(--slate-400); text-transform: uppercase;">Products</div>
                <div style="font-size: 2.5rem; font-weight: 800; color: var(--primary); margin-top: 8px;"><?php echo $productCount; ?></div>
            </div>
            <div class="card-premium">
                <div style="font-size: 12px; font-weight: 700; color: var(--slate-400); text-transform: uppercase;">Orders</div>
                <div style="font-size: 2.5rem; font-weight: 800; color: var(--accent); margin-top: 8px;"><?php echo $orderCount; ?></div>
            </div>
            <div class="card-premium">
                <div style="font-size: 12px; font-weight: 700; color: var(--slate-400); text-transform: uppercase;">Growth</div>
                <div style="font-size: 2.5rem; font-weight: 800; color: white; margin-top: 8px;">+14%</div>
            </div>
        </div>

        <div class="card-premium">
            <h3 style="font-size: 1.25rem; color: white; margin-bottom: 24px;">Recent System Events</h3>
            <div style="display: flex; flex-direction: column; gap: 16px;">
                <div style="padding: 16px; background: rgba(255,255,255,0.05); border-radius: 8px; border-left: 4px solid var(--primary);">
                    <div style="font-weight: 600; font-size: 14px; margin-bottom: 4px; color: white;">New User Registration</div>
                    <div style="font-size: 12px; color: var(--slate-400);">2 minutes ago</div>
                </div>
                <div style="padding: 16px; background: rgba(255,255,255,0.05); border-radius: 8px; border-left: 4px solid var(--accent);">
                    <div style="font-weight: 600; font-size: 14px; margin-bottom: 4px; color: white;">Inventory Low: Premium Soil</div>
                    <div style="font-size: 12px; color: var(--slate-400);">1 hour ago</div>
                </div>
            </div>
        </div>
    </main>
</div>

