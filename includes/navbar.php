<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$cart_count = 0;
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $qty) {
        $cart_count += $qty;
    }
}
?>
<link rel="stylesheet" href="assests/css/global.css">
<nav class="studio-navbar">
    <div class="studio-nav-container">
        <a href="index.php" class="studio-logo">
            <i class='bx bxs-leaf'></i> FloraAI
        </a>
        
        <ul class="studio-nav-links">
            <li><a href="store.php">Marketplace</a></li>
            <li><a href="cart.php" class="studio-cart">
                <i class='bx bx-shopping-bag'></i>
                <?php if($cart_count > 0): ?>
                    <span class="studio-cart-badge"><?php echo $cart_count; ?></span>
                <?php endif; ?>
            </a></li>
            
            <?php if(isset($_SESSION['user_id'])): ?>
                <li><a href="dashboard.php" class="btn-studio btn-studio-mint" style="padding: 10px 20px; font-size: 14px;">Go to Dashboard</a></li>
            <?php elseif(isset($_SESSION['partner_id'])): ?>
                <li><a href="delivery/dashboard.php" class="btn-studio btn-studio-mint">Delivery Hub</a></li>
            <?php else: ?>
                <li><a href="login.php" class="nav-auth-link">Sign In</a></li>
                <li><a href="register.php" class="btn-studio btn-studio-dark" style="padding: 10px 20px; font-size: 14px;">Join Now</a></li>
            <?php endif; ?>
        </ul>
    </div>
</nav>

<style>
    .studio-navbar {
        background: var(--slate-100);
        border-bottom: 1px solid rgba(255,255,255,0.05);
        height: 80px;
        display: flex;
        align-items: center;
        position: fixed;
        top: 0; left: 0; right: 0;
        z-index: 3000;
        backdrop-filter: blur(10px);
    }
    .studio-nav-container {
        max-width: 1200px;
        margin: 0 auto;
        width: 100%;
        padding: 0 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .studio-logo {
        color: var(--slate-900);
        text-decoration: none;
        font-weight: 800;
        font-size: 1.5rem;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .studio-logo i { color: var(--primary); }

    .studio-nav-links {
        display: flex;
        list-style: none;
        align-items: center;
        gap: 40px;
    }
    .studio-nav-links a {
        text-decoration: none;
        color: var(--slate-400);
        font-weight: 600;
        font-size: 15px;
        transition: 0.2s;
    }
    .studio-nav-links a:hover { color: white; }

    .studio-cart { position: relative; font-size: 22px !important; }
    .studio-cart-badge {
        position: absolute;
        top: -6px; right: -8px;
        background: #ef4444;
        color: white;
        font-size: 10px;
        width: 18px; height: 18px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        border: 2px solid var(--slate-100);
    }

    .nav-auth-link { color: var(--slate-400) !important; }
    .nav-auth-link:hover { color: white !important; }

    /* Button adjustments for dark navbar */
    .btn-studio {
        border-radius: 100px;
        font-weight: 600;
        text-decoration: none;
        transition: 0.2s;
    }
    .btn-studio-mint {
        background: var(--primary);
        color: white !important;
    }
    .btn-studio-mint:hover { background: #15803d; }
    
    .btn-studio-dark {
        background: rgba(255,255,255,0.1);
        color: white;
        border: 1px solid rgba(255,255,255,0.2);
    }
    .btn-studio-dark:hover { background: rgba(255,255,255,0.2); }

    @media (max-width: 768px) {
        .studio-nav-links { display: none; }
    }
</style>
