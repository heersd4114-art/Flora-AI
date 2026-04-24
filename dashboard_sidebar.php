<?php
if (!function_exists('isActive')) {
    function isActive($page) {
        return (basename($_SERVER['PHP_SELF']) == $page) ? 'active' : '';
    }
}
?>
<aside class="sidebar-panel" id="sidebar">
    <div style="padding: 32px; border-bottom: 1px solid rgba(255,255,255,0.05);">
        <h2 style="font-size: 1.5rem; font-weight: 800; color: white; display: flex; align-items: center; gap: 10px; margin: 0;">
            <i class='bx bxs-leaf' style="color: var(--primary);"></i> FloraAI
        </h2>
    </div>
    
    <nav style="flex: 1; padding: 24px; overflow-y: auto;">
        <div style="font-size: 11px; text-transform: uppercase; color: var(--slate-400); font-weight: 700; margin-bottom: 12px; letter-spacing: 0.05em;">Main Menu</div>
        
        <a href="dashboard.php" class="nav-item <?php echo isActive('dashboard.php'); ?>">
            <i class='bx bxs-dashboard'></i> Dashboard
        </a>
        <a href="identify.php" class="nav-item <?php echo isActive('identify.php'); ?>">
            <i class='bx bx-scan'></i> Identify Plant
        </a>
        <a href="disease.php" class="nav-item <?php echo isActive('disease.php'); ?>">
            <i class='bx bxs-virus-block'></i> Disease History
        </a>

        <div style="font-size: 11px; text-transform: uppercase; color: var(--slate-400); font-weight: 700; margin: 24px 0 12px; letter-spacing: 0.05em;">Marketplace</div>
        
        <a href="store.php" class="nav-item <?php echo isActive('store.php'); ?>">
            <i class='bx bxs-store'></i> Store
        </a>
        <a href="orders.php" class="nav-item <?php echo isActive('orders.php'); ?>">
            <i class='bx bxs-package'></i> My Orders
        </a>
        <a href="cart.php" class="nav-item <?php echo isActive('cart.php'); ?>">
            <i class='bx bxs-cart'></i> Cart
        </a>

        <div style="margin-top: auto; padding-top: 24px;">
            <a href="profile.php" class="nav-item <?php echo isActive('profile.php'); ?>">
                <i class='bx bxs-user-circle'></i> Profile
            </a>
            <a href="logout.php" class="nav-item" style="color: #ef4444;">
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

    // Floating Cart Logic
    document.addEventListener("DOMContentLoaded", function() {
        refreshCartBadge();
    });

    function refreshCartBadge() {
        fetch('cart_count.php')
        .then(response => response.json())
        .then(data => {
            let badge = document.getElementById('global-cart-badge');
            if(badge) {
                if(data.count > 0) {
                    badge.style.display = 'flex';
                    badge.innerText = data.count;
                } else {
                    badge.style.display = 'none';
                }
            }
        });
    }

    function addAjaxToCart(event, productId) {
        event.preventDefault();
        let btn = event.currentTarget;
        let originalHTML = btn.innerHTML;
        btn.innerHTML = "<i class='bx bx-loader-alt bx-spin'></i>";
        
        fetch('add_to_cart.php?ajax=true&id=' + productId)
        .then(response => response.json())
        .then(data => {
            if(data.status === 'success') {
                refreshCartBadge();
                btn.innerHTML = "<i class='bx bx-check'></i>";
                btn.style.background = "#4ade80"; // Turn green temporarily
                setTimeout(() => {
                    btn.innerHTML = originalHTML;
                    btn.style.background = ""; // restore
                }, 1500);
            }
        });
        return false;
    }
</script>

<!-- Floating Cart Button -->
<a href="cart.php" style="position: fixed; top: 20px; right: 20px; z-index: 9999; background: #1e293b; padding: 12px 16px; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.5); text-decoration: none; color: white; display: flex; align-items: center; gap: 8px; border: 1px solid rgba(255,255,255,0.1);">
    <i class='bx bxs-cart' style="font-size: 24px; color: var(--primary);"></i>
    <span style="font-weight: 700; font-size: 14px;">Cart</span>
    <div id="global-cart-badge" style="display: none; background: #ef4444; color: white; border-radius: 50%; width: 22px; height: 22px; justify-content: center; align-items: center; font-size: 11px; font-weight: 800; margin-left: 4px;">0</div>
</a>
