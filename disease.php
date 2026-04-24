<?php
session_start();
include "config.php";
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }

$user_id = $_SESSION['user_id'];
$sql = "SELECT ph.*, d.disease_id, p.product_id, p.name as product_name, p.price, p.image_url as product_image 
        FROM plant_history ph
        LEFT JOIN diseases d ON ph.disease_detected = d.name
        LEFT JOIN disease_treatments dt ON d.disease_id = dt.disease_id
        LEFT JOIN products p ON dt.product_id = p.product_id
        WHERE ph.user_id = ? 
        ORDER BY ph.scan_date DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$history_list = [];
while ($row = $result->fetch_assoc()) {
    $id = $row['id'];
    if (!isset($history_list[$id])) {
        $history_list[$id] = $row;
        $history_list[$id]['cures'] = [];
    }
    if ($row['product_id']) {
        // Prevent duplicate products if any
        $found = false;
        foreach($history_list[$id]['cures'] as $c) {
            if ($c['product_id'] == $row['product_id']) $found = true;
        }
        if (!$found) {
            $history_list[$id]['cures'][] = [
                'product_id' => $row['product_id'],
                'name' => $row['product_name'],
                'price' => $row['price'],
                'image_url' => $row['product_image']
            ];
        }
    }
}
$history_list = array_values($history_list);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Disease History | FloraAI</title>
    <link rel="stylesheet" href="assests/css/global.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>

<div class="dashboard-container">
    <?php include "dashboard_sidebar.php"; ?>

    <main class="main-content animate-enter">
        <header style="margin-bottom: 40px;">
            <h1 style="font-size: 2rem; font-weight: 700; color: white;">Care History</h1>
            <p style="color: var(--slate-400);">Previous diagnostics and treatment plans.</p>
        </header>

        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 24px;">
            <?php if (!empty($history_list)): ?>
                <?php foreach($history_list as $row): ?>
                <div class="card-premium" style="padding: 0; overflow: hidden; display: flex; flex-direction: column; position: relative;">
                    <a href="delete_disease.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Delete this record?');" style="position: absolute; top: 10px; right: 10px; color: #ef4444; background: rgba(0,0,0,0.5); border-radius: 50%; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; text-decoration: none;">
                        <i class='bx bxs-trash'></i>
                    </a>
                    <div style="padding: 24px; display: flex; gap: 16px; border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <img src="<?php echo !empty($row['image_path']) ? $row['image_path'] : 'uploads/default.png'; ?>" style="width: 64px; height: 64px; border-radius: 12px; object-fit: cover;">
                        <div>
                            <h3 style="font-size: 1.1rem; color: white; margin: 0 0 4px;"><?php echo htmlspecialchars($row['plant_name']); ?></h3>
                            <span style="font-size: 13px; color: var(--slate-400);"><?php echo date("M d, Y", strtotime($row['scan_date'])); ?></span>
                        </div>
                    </div>
                    
                    <div style="padding: 24px; flex: 1; display: flex; flex-direction: column;">
                        <div style="margin-bottom: 24px;">
                            <span style="background: rgba(239, 68, 68, 0.2); color: #f87171; padding: 4px 12px; border-radius: 100px; font-size: 12px; font-weight: 700;">
                                <i class='bx bxs-virus'></i> <?php echo htmlspecialchars($row['disease_detected']); ?>
                            </span>
                        </div>
                        
                        <?php if (!empty($row['ai_analysis'])): ?>
                            <div style="margin-bottom: 16px; background: rgba(74,222,128,0.05); padding: 12px; border-radius: 8px; border: 1px solid rgba(74,222,128,0.1);">
                                <div style="font-size: 11px; font-weight: 700; color: #4ade80; text-transform: uppercase; margin-bottom: 6px;"><i class='bx bxs-brain'></i> AI Analysis</div>
                                <p style="font-size: 12px; color: #e2e8f0; line-height: 1.6; margin: 0;"><?php echo htmlspecialchars($row['ai_analysis']); ?></p>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($row['treatment_steps'])): ?>
                            <?php $steps = json_decode($row['treatment_steps'], true); if ($steps && is_array($steps)): ?>
                            <div style="margin-bottom: 16px; padding: 12px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.05);">
                                <div style="font-size: 11px; font-weight: 700; color: var(--slate-400); text-transform: uppercase; margin-bottom: 6px;"><i class='bx bxs-shield-alt-2'></i> Treatment Steps</div>
                                <ul style="margin: 0; padding-left: 14px; color: #cbd5e1; font-size: 12px; line-height: 1.5;">
                                    <?php foreach($steps as $step): ?>
                                        <li><?php echo htmlspecialchars($step); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                            <?php endif; ?>
                        <?php endif; ?>

                        <?php if(!empty($row['cures'])): ?>
                            <div style="margin-top: auto; padding-top: 16px; border-top: 1px solid rgba(255,255,255,0.05);">
                                <div style="font-size: 13px; font-weight: 600; color: var(--slate-400); margin-bottom: 8px;">Recommended Cures:</div>
                                <div style="display: flex; flex-direction: column; gap: 8px;">
                                <?php foreach($row['cures'] as $cure): ?>
                                    <div style="display: flex; justify-content: space-between; align-items: center; background: rgba(0,0,0,0.2); padding: 8px; border-radius: 8px;">
                                        <div style="display: flex; align-items: center; gap: 10px;">
                                            <img src="<?php echo $cure['image_url']; ?>" style="width: 36px; height: 36px; border-radius: 6px; object-fit: cover;">
                                            <div>
                                                <div style="font-weight: 700; color: white; font-size: 12px;"><?php echo htmlspecialchars($cure['name']); ?></div>
                                                <div style="font-weight: 700; color: var(--primary); font-size: 11px;">₹<?php echo htmlspecialchars($cure['price']); ?></div>
                                            </div>
                                        </div>
                                        <a href="#" onclick="return addAjaxToCart(event, <?php echo $cure['product_id']; ?>);" class="btn-primary" style="padding: 6px 12px; font-size: 11px;"><i class='bx bxs-cart-add'></i> Add</a>
                                    </div>
                                <?php endforeach; ?>
                                </div>
                            </div>
                        <?php else: ?>
                            <div style="margin-top: auto; color: var(--slate-400); font-size: 13px; font-style: italic;">
                                No specific product available. <a href="store.php" style="color: var(--primary);">Browse Store</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No diagnostics found.</p>
            <?php endif; ?>
        </div>
    </main>
</div>

</body>
</html>
