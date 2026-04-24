<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
include "config.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Identify Plant | FloraAI</title>
    <link rel="stylesheet" href="assests/css/global.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>

<div class="dashboard-container">
    <?php include "dashboard_sidebar.php"; ?>

    <main class="main-content animate-enter">
        <header style="margin-bottom: 40px;">
            <h1 style="font-size: 2rem; font-weight: 700; color: white;">Health Analysis</h1>
            <p style="color: var(--slate-400);">Initiate a new diagnostic sequence.</p>
        </header>

        <form action="process_identify.php" method="POST" enctype="multipart/form-data">
            <div class="card-premium" style="max-width: 600px; margin: 0 auto; text-align: center; padding: 60px 40px; border-style: dashed; border-width: 2px;">
                <i class='bx bx-scan' style="font-size: 64px; color: var(--primary); margin-bottom: 24px; display: inline-block; padding: 20px; background: rgba(34, 197, 94, 0.1); border-radius: 50%;"></i>
                <h2 style="font-size: 1.5rem; color: white; margin-bottom: 12px;">Upload Specimen</h2>
                <p style="color: var(--slate-400); margin-bottom: 32px;">Please ensure the image is clear and focused on the affected area.</p>
                
                <label for="plant_image" class="btn-primary" style="cursor: pointer;">
                    <i class='bx bx-upload'></i> Select Image
                </label>
                <input type="file" id="plant_image" name="plant_image" accept="image/*" style="display:none;" onchange="previewSpecimen(this)" required>
                
                <div id="preview-area" style="display:none; margin-top: 40px; pt-4; border-top: 1px solid #f1f5f9;">
                    <img id="preview-img-studio" style="max-width: 100%; border-radius: 12px; box-shadow: var(--shadow-md);">
                    <br><br>
                    <button type="submit" class="btn-primary" style="width: 100%; justify-content: center; font-size: 16px; padding: 16px;">Run Analysis</button>
                    <div id="loading" style="display:none; margin-top: 20px; padding: 20px; background: rgba(34,197,94,0.08); border: 1px solid rgba(34,197,94,0.2); border-radius: 12px;">
                        <div style="display:flex; align-items:center; gap: 12px; justify-content:center; color: #4ade80; font-weight: 700; font-size: 1rem; margin-bottom: 8px;">
                            <i class='bx bx-loader-alt bx-spin' style="font-size:24px;"></i>
                            Flora AI is analysing your plant...
                        </div>
                        <p style="color: var(--slate-400); font-size: 0.85rem; margin: 0; text-align:center;">
                            Flora AI powered by Google Gemini is identifying your plant.<br>
                            <strong style="color: white;">Please wait 10–30 seconds</strong> — do not close or refresh this page.
                        </p>
                    </div>
                </div>
            </div>
        </form>
    </main>
</div>

<script>
    function previewSpecimen(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                const img = document.getElementById('preview-img-studio');
                img.src = e.target.result;
                document.getElementById('preview-area').style.display = 'block';
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
    document.querySelector('form').addEventListener('submit', function() {
        document.querySelector('button[type="submit"]').style.display = 'none';
        document.getElementById('loading').style.display = 'block';
    });
</script>

</body>
</html>
