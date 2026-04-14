<?php
session_start();
include "../config.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit;
}

$msg = "";

if (isset($_POST['submit'])) {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $product_id = $_POST['product_id'];

    $sql = "INSERT INTO diseases (name, description) VALUES ('$name', '$description')";
    if ($conn->query($sql) === TRUE) {
        $disease_id = $conn->insert_id;
        
        // Link Treatment
        if(!empty($product_id)){
             $sql2 = "INSERT INTO disease_treatments (disease_id, product_id) VALUES ('$disease_id', '$product_id')";
             $conn->query($sql2);
        }
        $msg = "Pathogen added successfully.";
    } else {
        $msg = "Error: " . $conn->error;
    }
}

// Get Products for dropdown
$products = $conn->query("SELECT * FROM products");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Disease | FloraAI Admin</title>
    <link rel="stylesheet" href="../assests/css/global.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>

<div class="dashboard-container">
    <?php include "includes/sidebar.php"; ?>

    <main class="main-content animate-enter">
        <header style="margin-bottom: 40px; display: flex; align-items: center; gap: 16px;">
            <a href="diseases.php" style="color: var(--slate-400); font-size: 24px;"><i class='bx bx-arrow-back'></i></a>
            <div>
                <h1 style="font-size: 2rem; font-weight: 700; color: var(--slate-900);">Add Pathogen</h1>
                <p style="color: var(--slate-400);">Register a new disease profile.</p>
            </div>
        </header>

        <?php if ($msg) echo "<div style='background: #f0fdf4; color: #166534; padding: 12px 16px; border-radius: 8px; margin-bottom: 24px; font-weight: 600; border: 1px solid #bbf7d0;'><i class='bx bx-check-circle'></i> $msg</div>"; ?>

        <div class="card-premium" style="max-width: 800px;">
            <form method="POST">
                <div style="margin-bottom: 24px;">
                    <label style="display: block; font-weight: 600; color: var(--slate-600); margin-bottom: 8px;">Disease Name</label>
                    <input type="text" name="name" required style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px;">
                </div>

                <div style="margin-bottom: 24px;">
                    <label style="display: block; font-weight: 600; color: var(--slate-600); margin-bottom: 8px;">Symptoms & Description</label>
                    <textarea name="description" rows="5" required style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px;"></textarea>
                </div>

                <div style="margin-bottom: 32px;">
                    <label style="display: block; font-weight: 600; color: var(--slate-600); margin-bottom: 8px;">Recommended Treatment (Product)</label>
                    <select name="product_id" style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px;">
                        <option value="">-- No Specific Treatment --</option>
                        <?php while($p = $products->fetch_assoc()): ?>
                            <option value="<?php echo $p['product_id']; ?>"><?php echo $p['name']; ?> - ₹<?php echo $p['price']; ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <button type="submit" name="submit" class="btn-primary" style="padding: 14px 32px;">Save Profile</button>
            </form>
        </div>
    </main>
</div>

</body>
</html>
