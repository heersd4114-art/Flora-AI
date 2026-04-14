<?php
// Function to detect active page
if (!function_exists('isActiveAdmin')) {
    function isActiveAdmin($page) {
        if (basename($_SERVER['PHP_SELF']) == $page) {
            return 'active';
        }
        return '';
    }
}
?>
<link rel="stylesheet" href="../assests/css/global.css">
<aside class="sidebar-panel" id="sidebar">
    <div style="padding: 32px; border-bottom: 1px solid rgba(255,255,255,0.05);">
        <h2 style="font-size: 1.5rem; font-weight: 800; color: white; display: flex; align-items: center; gap: 10px; margin: 0;">
            <i class='bx bxs-leaf' style="color: var(--primary);"></i> Flora Admin
        </h2>
    </div>
    
    <nav style="flex: 1; padding: 24px; overflow-y: auto;">
        <div style="font-size: 11px; text-transform: uppercase; color: var(--slate-400); font-weight: 700; margin-bottom: 12px; letter-spacing: 0.05em;">Management</div>
        
        <a href="dashboard.php" class="nav-item <?php echo isActiveAdmin('dashboard.php'); ?>">
            <i class='bx bxs-dashboard'></i> Dashboard
        </a>
        <a href="products.php" class="nav-item <?php echo isActiveAdmin('products.php'); ?>">
            <i class='bx bxs-shopping-bag'></i> Products
        </a>
        <a href="diseases.php" class="nav-item <?php echo isActiveAdmin('diseases.php'); ?>">
            <i class='bx bxs-virus'></i> Diseases
        </a>

        <div style="font-size: 11px; text-transform: uppercase; color: var(--slate-400); font-weight: 700; margin: 24px 0 12px; letter-spacing: 0.05em;">Operations</div>
        
        <a href="orders.php" class="nav-item <?php echo isActiveAdmin('orders.php'); ?>">
            <i class='bx bxs-package'></i> Orders
        </a>
        <a href="users.php" class="nav-item <?php echo isActiveAdmin('users.php'); ?>">
            <i class='bx bxs-user-detail'></i> Users
        </a>
        <a href="delivery.php" class="nav-item <?php echo isActiveAdmin('delivery.php'); ?>">
            <i class='bx bxs-truck'></i> Delivery
        </a>

        <div style="margin-top: auto; padding-top: 24px;">
            <a href="profile.php" style="display: flex; align-items: center; gap: 12px; padding: 16px; background: rgba(255,255,255,0.05); border-radius: 12px; margin-bottom: 12px; text-decoration: none; transition: 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.1)'" onmouseout="this.style.background='rgba(255,255,255,0.05)'">
                <div style="width: 40px; height: 40px; background: rgba(255,255,255,0.1); color: white; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-weight: 700;">A</div>
                <div>
                    <div style="font-size: 13px; font-weight: 700; color: white;">Root Admin</div>
                    <div style="font-size: 11px; color: var(--primary);">Edit Profile</div>
                </div>
            </a>
            <a href="../logout.php" class="nav-item" style="color: #ef4444;">
                <i class='bx bxs-log-out'></i> Logout
            </a>
        </div>
    </nav>
</aside>

<style>
    .nav-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 14px 16px;
        color: var(--slate-400);
        text-decoration: none;
        font-weight: 600;
        border-radius: 12px;
        margin-bottom: 4px;
        transition: all 0.2s;
    }
    .nav-item:hover, .nav-item.active {
        background: rgba(34, 197, 94, 0.15);
        color: white;
    }
    .nav-item i { font-size: 20px; }
</style>

<script>
    function toggleSidebar() {
        document.getElementById('sidebar').classList.toggle('active');
    }
</script>
