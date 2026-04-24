<?php
session_start();
set_time_limit(90); // Allow 90s for Gemini API processing
include "config.php";
include "config_api.php";

if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['plant_image'])) {
    
    $target_dir = "uploads/";
    if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
    $filename = uniqid() . "_" . basename($_FILES["plant_image"]["name"]);
    $target_file = $target_dir . $filename;
    
    if (move_uploaded_file($_FILES["plant_image"]["tmp_name"], $target_file)) {

        $disease_detected = "Healthy/Unknown";
        $plant_name = "Identifying...";
        $care_tips = "Keep your plant watered and in sunlight.";
        $cures = false;
        $fallback = false;
        $ai_analysis = "";
        $confidence_level = "";
        $severity = "";
        $treatment_steps = [];
        $confidence = 0;

        // --- CONFIGURATION ---
        $local_ai_url = 'http://127.0.0.1:5001/predict'; // NEW PORT 5001
        $api_success = false;

        /* 
        // DISABLED EXTERNAL API FOR TESTING LOCAL LOGIC
        if (defined('PLANT_ID_API_KEY') && PLANT_ID_API_KEY != 'YOUR_PLANT_ID_API_KEY_HERE') {
             // ... Code hidden to force local ...
        }
        */

        // --- LOCAL AI (PRIMARY) ---
        if (!$api_success) {
            $ch_local = curl_init($local_ai_url);
            $cfile = new CURLFile($target_file, mime_content_type($target_file), basename($target_file));
            $data_local = array('image' => $cfile);
            
            curl_setopt($ch_local, CURLOPT_POST, 1);
            curl_setopt($ch_local, CURLOPT_POSTFIELDS, $data_local);
            curl_setopt($ch_local, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch_local, CURLOPT_TIMEOUT, 300); // 300s — Phi-3-mini can take > 120s on some CPUs
            
            $headers = [
                "X-API-Key: " . (defined('GEMINI_API_KEY') ? GEMINI_API_KEY : '')
            ];
            curl_setopt($ch_local, CURLOPT_HTTPHEADER, $headers);
            
            $result_local = curl_exec($ch_local);
            $http_code = curl_getinfo($ch_local, CURLINFO_HTTP_CODE);
            curl_close($ch_local);
            
            if ($http_code == 200 && $result_local) {
                $decoded = json_decode($result_local, true);
                if (isset($decoded['plant_name'])) {
                    // Parse all fields from the Flora AI response
                    $plant_name       = $decoded['display_name'] ?? $decoded['plant_name'];
                    $disease_detected = $decoded['disease'];
                    $care_tips        = $decoded['care_tips'] ?? '';
                    if (is_array($care_tips)) {
                        $care_tips = implode(" ", $care_tips);
                    }
                    $ai_analysis      = $decoded['ai_analysis'] ?? '';
                    $confidence_level = $decoded['confidence_level'] ?? '';
                    $severity         = $decoded['severity'] ?? '';
                    $confidence       = round(($decoded['confidence'] ?? 0) * 100, 1);
                    $treatment_steps  = $decoded['treatment_steps'] ?? [];
                    if (is_string($treatment_steps)) {
                        $treatment_steps = [$treatment_steps];
                    }
                    $db_term          = $decoded['search_term'] ?? $disease_detected;

                    $api_success = true;

                    if ($plant_name == "Unknown Object" || $disease_detected == "Not a Plant") {
                        $plant_name = "Scan Error";
                        $disease_detected = "Invalid Image";
                        $care_tips = "We could not detect a plant in this image. Please re-upload a clearer photo.";
                        $treatment_steps = [];
                        $cures = false;
                    } else {
                        // Legacy term remapping
                        if ($db_term == "Needs Food")    $db_term = "Nutrient Deficiency";
                        if ($db_term == "Infection")     $db_term = "Leaf Spot";
                        if ($db_term == "Fungal Growth") $db_term = "Powdery Mildew";

                        $stmt = $conn->prepare("SELECT p.* FROM diseases d
                                                JOIN disease_treatments dt ON d.disease_id = dt.disease_id
                                                JOIN products p ON dt.product_id = p.product_id
                                                WHERE d.name LIKE CONCAT('%', ?, '%') LIMIT 3");
                        $stmt->bind_param("s", $db_term);
                        $stmt->execute();
                        $cures = $stmt->get_result();
                    }
                }
            }
        }

        if (!$api_success) { 
             // Final Fallback: Simulated Mock only if Local AI works but returns garbage, or is offline
             $fallback = true; 
        }

        if ($fallback) {
            // EXPLICT ERROR IF FLASK AI SERVICE IS DOWN OR TIMED OUT
            $plant_name = "AI Service Error"; 
            $disease_detected = "Service Offline/Timeout";
            $care_tips = "The Flora AI Service could not process your image. Please ensure the Windows batch file is running and try again, or wait a bit longer.";
            $cures = false;
        }

        // SAVE TO HISTORY (Including detailed AI insights)
        $steps_json = !empty($treatment_steps) ? json_encode($treatment_steps) : null;
        $stmt_save = $conn->prepare("INSERT INTO plant_history (user_id, plant_name, disease_detected, image_path, ai_analysis, treatment_steps, care_tips) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt_save->bind_param("issssss", $_SESSION['user_id'], $plant_name, $disease_detected, $target_file, $ai_analysis, $steps_json, $care_tips);
        $stmt_save->execute();

    } else {
        echo "File upload failed."; exit;
    }
} else {
    header("Location: identify.php"); exit;
}
?>
<link rel="stylesheet" href="assests/css/global.css">
<style>
    body { background: var(--bg-main); }
    .result-glass-card {
        background: #1e293b;
        border-radius: var(--radius-lg);
        padding: 48px;
        box-shadow: 0 20px 40px -15px rgba(0,0,0,0.3);
        border: 1px solid rgba(255,255,255,0.1);
        max-width: 900px;
        margin: 0 auto;
        animation: fadeIn 0.8s ease-out;
    }
    .report-img-wrapper {
        width: 100%;
        max-width: 400px;
        height: 300px;
        margin: 0 auto 32px;
        border-radius: var(--radius-md);
        overflow: hidden;
        box-shadow: 0 12px 24px -10px rgba(0,0,0,0.3);
        border: 1px solid rgba(255,255,255,0.1);
    }
    .report-img-wrapper img { width: 100%; height: 100%; object-fit: cover; }
    
    .status-header-premium { text-align: center; margin-bottom: 40px; }
    .diagnosis-badge {
        display: inline-flex;
        align-items: center;
        gap: 12px;
        font-size: 2rem;
        font-weight: 800;
        color: #f87171;
        margin-bottom: 8px;
    }
    .status-healthy { color: #4ade80; }

    .insight-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 32px;
        margin-top: 40px;
        border-top: 1px solid rgba(255,255,255,0.1);
        padding-top: 32px;
    }
    .insight-card {
        padding: 24px;
        border-radius: var(--radius-md);
        background: rgba(255,255,255,0.05);
        border: 1px solid rgba(255,255,255,0.05);
    }
    .insight-card h4 {
        margin-bottom: 12px;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #94a3b8;
        display: flex;
        align-items: center;
        gap: 8px;
        font-weight: 700;
    }
</style>

<div class="dashboard-container">
    <?php include "dashboard_sidebar.php"; ?>
    <main class="main-content animate-enter">
        <header class="header-premium" style="margin-bottom: 48px;">
            <div class="welcome-section">
                <h1 class="premium-title">Diagnostic Intelligence Report</h1>
                <p class="premium-subtitle">Neural analysis completed with high-fidelity health assessment.</p>
            </div>
        </header>

        <div class="result-glass-card">
            <div class="status-header-premium">
                <div class="report-img-wrapper">
                    <img src="<?php echo $target_file; ?>">
                </div>
                <div class="diagnosis-badge <?php echo ($disease_detected == 'Healthy') ? 'status-healthy' : (($disease_detected == 'Invalid Image' || $plant_name == 'Scan Error') ? 'status-error' : ''); ?>" style="<?php echo ($disease_detected == 'Invalid Image') ? 'color: #ef4444;' : ''; ?>">
                    <i class='bx <?php echo ($disease_detected == 'Healthy') ? 'bx-check-double' : (($disease_detected == 'Invalid Image') ? 'bx-error' : 'bxs-hot'); ?>'></i>
                    <?php echo $disease_detected; ?>
                </div>
                <p style="font-size: 1.1rem; color: var(--slate-400); font-weight: 600;">Specimen Identified: <strong style="color: white;"><?php echo $plant_name; ?></strong></p>
            </div>
            
            <?php if(!empty($ai_analysis)): ?>
            <div style="margin: 32px 0; padding: 24px; border-radius: 12px; background: rgba(74,222,128,0.07); border: 1px solid rgba(74,222,128,0.2);">
                <h4 style="margin-bottom: 10px; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em; color: #4ade80; display: flex; align-items: center; gap: 8px; font-weight: 700;">
                    <i class='bx bxs-brain'></i> Flora AI Analysis
                    <?php if($confidence_level): ?><span style="background: rgba(74,222,128,0.15); padding: 2px 10px; border-radius: 999px; font-size: 0.75rem;"><?php echo $confidence; ?>% &mdash; <?php echo $confidence_level; ?></span><?php endif; ?>
                    <?php if($severity && $severity != 'None'): ?><span style="background: rgba(248,113,113,0.15); color: #f87171; padding: 2px 10px; border-radius: 999px; font-size: 0.75rem;"><?php echo $severity; ?> Severity</span><?php endif; ?>
                </h4>
                <p style="line-height: 1.8; color: #e2e8f0; font-size: 1rem;"><?php echo htmlspecialchars($ai_analysis); ?></p>
            </div>
            <?php endif; ?>

            <div class="insight-row">
                <div class="insight-card">
                    <h4><i class='bx bxs-shield-alt-2'></i> Treatment Steps</h4>
                    <?php if(!empty($treatment_steps)): ?>
                        <ol style="padding-left: 18px; margin: 0; color: white; line-height: 2;">
                            <?php foreach($treatment_steps as $step): ?>
                                <li><?php echo htmlspecialchars($step); ?></li>
                            <?php endforeach; ?>
                        </ol>
                    <?php else: ?>
                        <p style="color: white; line-height: 1.6;"><?php echo $care_tips; ?></p>
                    <?php endif; ?>
                </div>

                <div class="insight-card">
                    <h4><i class='bx bxs-capsule'></i> Direct Cures (Store)</h4>
                    <?php if($cures && $cures->num_rows > 0): ?>
                        <div style="display: flex; flex-direction: column; gap: 12px;">
                            <?php while($prod = $cures->fetch_assoc()): ?>
                            <div style="display: flex; align-items: center; justify-content: space-between; gap: 12px; background: rgba(0,0,0,0.2); padding: 12px; border-radius: 10px; border: 1px solid rgba(255,255,255,0.05);">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <img src="<?php echo $prod['image_url']; ?>" style="width: 48px; height: 48px; border-radius: 8px; object-fit: cover;">
                                    <div>
                                        <div style="font-weight: 700; font-size: 14px; color: white;"><?php echo $prod['name']; ?></div>
                                        <div style="font-size: 13px; color: var(--primary); font-weight: 700;">&#8377;<?php echo $prod['price']; ?></div>
                                    </div>
                                </div>
                                <a href="add_to_cart.php?id=<?php echo $prod['product_id']; ?>" class="btn-premium btn-primary" style="padding: 8px 16px; font-size: 13px; border-radius: 6px; text-decoration: none;">
                                    <i class='bx bxs-cart-add'></i> Add to Cart
                                </a>
                            </div>
                            <?php endwhile; ?>
                        </div>
                    <?php else: ?>
                        <p style="color: var(--text-muted); font-size: 14px;">No specific product found. Browse our store for remedies.</p>
                        <a href="store.php" class="btn-premium btn-secondary" style="margin-top: 12px; display: inline-flex; color: white !important;">Explore Marketplace</a>
                    <?php endif; ?>
                </div>
            </div>

            <div style="margin-top: 40px; text-align: center;">
                <a href="identify.php" class="glass-btn" style="text-decoration: none; display: inline-flex; align-items: center; gap: 8px; color: white !important;">
                    <i class='bx bx-rotate-left'></i> Perform New Session
                </a>
            </div>
        </div>
    </main>
</div>

