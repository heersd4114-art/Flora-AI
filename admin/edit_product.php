<?php
session_start();
include "../config.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit;
}

$id = $_GET['id'];
$sql = "SELECT * FROM products WHERE product_id=$id";
$result = $conn->query($sql);
$row = $result->fetch_assoc();

$msg = "";

if (isset($_POST['update'])) {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $type = $_POST['type'];

    if (!empty($_FILES["image"]["name"])) {
        $target_dir = "../uploads/";
        $image_name = basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $image_name;
        move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
        $image_url = "uploads/" . $image_name;
        $updateImg = ", image_url='$image_url'";
    } else {
        $updateImg = "";
    }

    $sql = "UPDATE products SET name='$name', description='$description', price='$price', stock_quantity='$stock', type='$type' $updateImg WHERE product_id=$id";

    if ($conn->query($sql) === TRUE) {
        $msg = "Product updated successfully.";
        // Refresh data
        $row['name'] = $name;
        $row['description'] = $description;
        $row['price'] = $price;
        $row['stock_quantity'] = $stock;
        $row['type'] = $type;
    } else {
        $msg = "Error updating record: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Product | FloraAI Admin</title>
    <link rel="stylesheet" href="../assests/css/global.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>

<div class="dashboard-container">
    <?php include "includes/sidebar.php"; ?>

    <main class="main-content animate-enter">
        <header style="margin-bottom: 40px; display: flex; align-items: center; gap: 16px;">
            <a href="products.php" style="color: var(--slate-400); font-size: 24px;"><i class='bx bx-arrow-back'></i></a>
            <div>
                <h1 style="font-size: 2rem; font-weight: 700; color: var(--slate-900);">Edit Product</h1>
                <p style="color: var(--slate-400);">Modify #<?php echo $row['product_id']; ?> details.</p>
            </div>
        </header>

        <?php if ($msg) echo "<div style='background: #f0fdf4; color: #166534; padding: 12px 16px; border-radius: 8px; margin-bottom: 24px; font-weight: 600; border: 1px solid #bbf7d0;'><i class='bx bx-check-circle'></i> $msg</div>"; ?>

        <div class="card-premium" style="max-width: 800px;">
            <form method="POST" enctype="multipart/form-data">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 24px;">
                    <div>
                        <label style="display: block; font-weight: 600; color: var(--slate-600); margin-bottom: 8px;">Product Name</label>
                        <input type="text" name="name" value="<?php echo htmlspecialchars($row['name']); ?>" required style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px;">
                    </div>
                    <div>
                        <label style="display: block; font-weight: 600; color: var(--slate-600); margin-bottom: 8px;">Type</label>
                        <select name="type" required style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px;">
                            <option value="Pesticide" <?php if($row['type']=='Pesticide') echo 'selected'; ?>>Pesticide</option>
                            <option value="Fertilizer" <?php if($row['type']=='Fertilizer') echo 'selected'; ?>>Fertilizer</option>
                            <option value="Tool" <?php if($row['type']=='Tool') echo 'selected'; ?>>Tool</option>
                        </select>
                    </div>
                </div>

                <div style="margin-bottom: 24px;">
                    <label style="display: block; font-weight: 600; color: var(--slate-600); margin-bottom: 8px;">Description</label>
                    <textarea name="description" rows="4" required style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px;"><?php echo htmlspecialchars($row['description']); ?></textarea>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 24px;">
                    <div>
                        <label style="display: block; font-weight: 600; color: var(--slate-600); margin-bottom: 8px;">Price</label>
                        <input type="number" step="0.01" name="price" value="<?php echo $row['price']; ?>" required style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px;">
                    </div>
                    <div>
                        <label style="display: block; font-weight: 600; color: var(--slate-600); margin-bottom: 8px;">Stock</label>
                        <input type="number" name="stock" value="<?php echo $row['stock_quantity']; ?>" required style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px;">
                    </div>
                </div>

                <div style="margin-bottom: 32px;">
                    <label style="display: block; font-weight: 600; color: var(--slate-600); margin-bottom: 8px;">Update Image (Optional)</label>
                    <input type="file" name="image" style="width: 100%; padding: 12px; border: 1px dashed #cbd5e1; border-radius: 8px;">
                </div>

                <button type="submit" name="update" class="btn-primary" style="padding: 14px 32px;">Save Updates</button>
            </form>
        </div>
    </main>
</div>

</body>
</html>
