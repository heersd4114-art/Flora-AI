<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flora Intelligence | Professional Botanical Care</title>
    <link rel="stylesheet" href="assests/css/global.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        .hero-studio {
            background: var(--studio-slate);
            color: white;
            padding: 160px 0 100px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        .hero-studio h1 { font-size: clamp(3rem, 6vw, 5rem); font-weight: 800; line-height: 1.1; margin-bottom: 24px; }
        .hero-studio p { font-size: 1.25rem; color: var(--studio-muted); max-width: 700px; margin: 0 auto 40px; }
        
        .hero-img-box {
            max-width: 1000px;
            margin: 0 auto -200px;
            position: relative;
            z-index: 10;
        }
        .hero-img-box img {
            width: 100%;
            border-radius: 30px;
            box-shadow: 0 50px 100px -20px rgba(0,0,0,0.5);
        }

        .services-studio { padding: 300px 0 120px; background: white; }
        .service-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 40px; }
        
        .showcase-studio { padding: 120px 0; background: #f8fafc; }
        .product-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 30px; }

        .footer-studio { background: var(--studio-slate); color: white; padding: 100px 0 40px; }
        .footer-grid { display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 60px; margin-bottom: 80px; }
        .footer-bottom { border-top: 1px solid rgba(255,255,255,0.05); padding-top: 40px; display: flex; justify-content: space-between; font-size: 14px; color: #64748b; }

        @media (max-width: 900px) {
            .footer-grid { grid-template-columns: 1fr; gap: 40px; }
            .hero-img-box { margin-bottom: -100px; }
            .services-studio { padding-top: 150px; }
        }
    </style>
    <?php include "includes/header_pwa.php"; ?>
</head>
<body>

    <?php include "includes/navbar.php"; ?>

    <section class="hero-studio">
        <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 0 24px;">
            <div class="badge" style="background: rgba(34, 197, 94, 0.15); color: #4ade80; padding: 8px 16px; border-radius: 100px; font-weight: 800; font-size: 13px; display: inline-block; margin-bottom: 24px; text-transform: uppercase; letter-spacing: 0.1em; border: 1px solid rgba(34, 197, 94, 0.3);">Modern Plant Intelligence</div>
            <h1 class="animate-studio">Welcome to <br><span class="text-gradient" style="font-size: 1.2em;">FloraAI</span></h1>
            <p class="animate-studio" style="animation-delay: 0.1s; color: #94a3b8;">Experience the future of plant care with our advanced AI diagnostic engine and curated marketplace.</p>
            <div class="btns-row" style="display: flex; gap: 16px; justify-content: center; margin-bottom: 80px;">
                <a href="login.php" class="btn-primary" style="padding: 16px 40px; font-size: 1.1rem;">Get Started</a>
                <a href="store.php" style="display: inline-flex; align-items: center; justify-content: center; padding: 16px 40px; border-radius: 100px; background: rgba(255,255,255,0.05); color: white; text-decoration: none; font-weight: 600; transition: 0.2s; border: 1px solid rgba(255,255,255,0.1);">View Store</a>
            </div>
            <div class="hero-img-box animate-studio" style="animation-delay: 0.2s;">
                <img src="https://images.unsplash.com/photo-1466692476868-aef1dfb1e735?q=80&w=2070&auto=format&fit=crop" alt="FloraAI Dark Mode Interface">
            </div>
        </div>
    </section>

    <section class="services-studio" style="background: var(--slate-50);">
        <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 0 24px;">
            <div style="text-align: center; margin-bottom: 80px;">
                <h2 style="font-size: 2.5rem; color: white; margin-bottom: 16px;">FloraAI Capabilities</h2>
                <p style="color: #94a3b8; max-width: 600px; margin: 0 auto;">Everything you need to maintain a high-performing garden.</p>
            </div>
            <div class="service-grid">
                <div class="card-premium">
                    <i class='bx bx-scan' style="font-size: 40px; color: #4ade80; margin-bottom: 24px; display: block;"></i>
                    <h3 style="margin-bottom: 12px; font-size: 1.25rem;">AI Diagnostics</h3>
                    <p style="color: #cbd5e1;">Identify plants and detect potential pathologies instantly.</p>
                </div>
                <div class="card-premium">
                    <i class='bx bx-package' style="font-size: 40px; color: #818cf8; margin-bottom: 24px; display: block;"></i>
                    <h3 style="margin-bottom: 12px; font-size: 1.25rem;">Flora Store</h3>
                    <p style="color: #cbd5e1;">Access expert-approved nutrients and tools directly.</p>
                </div>
                <div class="card-premium">
                    <i class='bx bx-history' style="font-size: 40px; color: #fbbf24; margin-bottom: 24px; display: block;"></i>
                    <h3 style="margin-bottom: 12px; font-size: 1.25rem;">Smart History</h3>
                    <p style="color: #cbd5e1;">Track every scan and health check in your digital log.</p>
                </div>
            </div>
        </div>
    </section>

    <footer class="footer-studio" style="background: #020617;">
        <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 0 24px;">
            <div class="footer-grid">
                <div>
                    <h3 style="margin-bottom: 24px; font-size: 1.5rem;"><i class='bx bxs-leaf' style="color: #22c55e;"></i> FloraAI</h3>
                    <p style="color: #64748b; line-height: 1.8;">Powered by Advanced Intelligence.</p>
                </div>
                <div>
                    <h4 style="margin-bottom: 24px; font-size: 1.1rem;">Quick Links</h4>
                    <ul style="list-style: none;">
                        <li style="margin-bottom: 12px;"><a href="store.php" style="color: #94a3b8; text-decoration: none;">Botany Store</a></li>
                        <li style="margin-bottom: 12px;"><a href="identify.php" style="color: #94a3b8; text-decoration: none;">Scan Plant</a></li>
                        <li style="margin-bottom: 12px;"><a href="login.php" style="color: #94a3b8; text-decoration: none;">Account Access</a></li>
                    </ul>
                </div>
                <div>
                    <h4 style="margin-bottom: 24px; font-size: 1.1rem;">Legal</h4>
                    <ul style="list-style: none;">
                        <li style="margin-bottom: 12px;"><a href="#" style="color: #94a3b8; text-decoration: none;">Terms of Use</a></li>
                        <li style="margin-bottom: 12px;"><a href="#" style="color: #94a3b8; text-decoration: none;">Privacy Policy</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <span>&copy; 2026 Flora Intelligence Studio.</span>
                <div style="display: flex; gap: 24px; font-size: 20px;">
                    <i class='bx bxl-instagram'></i>
                    <i class='bx bxl-linkedin'></i>
                </div>
            </div>
        </div>
    </footer>

</body>
</html>
