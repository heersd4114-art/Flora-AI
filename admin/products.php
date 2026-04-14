<?php
session_start();
include "../config.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit;
}

// Fetch Products
$sql = "SELECT * FROM products ORDER BY product_id DESC";
$result = $conn->query($sql);
?>
<link rel="stylesheet" href="../assests/css/global.css">
<style>
    body { background: var(--bg-main); overflow-x: hidden; }
    .management-content { animation: fadeIn 0.8s ease-out; }
    .table-glass-admin {
        background: white;
        border-radius: var(--radius-lg);
        overflow: hidden;
        border: 1px solid var(--glass-border);
        box-shadow: 0 20px 40px -20px rgba(0,0,0,0.05);
    }
    .admin-table { width: 100%; border-collapse: collapse; }
    .admin-table th {
        background: #f8fafc;
        padding: 20px;
        text-align: left;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        font-weight: 800;
        color: var(--text-muted);
        border-bottom: 2px solid #f1f5f9;
    }
    .admin-table td { padding: 20px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
    .product-img-admin { width: 48px; height: 48px; border-radius: 12px; object-fit: cover; border: 1px solid #e2e8f0; }
    
    .status-capsule {
        padding: 6px 12px;
        border-radius: var(--radius-full);
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
    }
    .status-ok { background: #f0fdf4; color: #166534; }
    .status-warn { background: #fff7ed; color: #ea580c; }
    
    .action-group { display: flex; gap: 8px; }
    .btn-icon-admin {
        width: 36px; height: 36px;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        text-decoration: none;
        transition: var(--transition-smooth);
        font-size: 18px;
    }
    .btn-edit { background: #eff6ff; color: #2563eb; }
    .btn-delete { background: #fff1f2; color: #e11d48; }
    .btn-edit:hover { background: #2563eb; color: white; }
    .btn-delete:hover { background: #e11d48; color: white; }
</style>

<div class="dashboard-container">
    <?php include "includes/sidebar.php"; ?>

    <main class="main-content animate-enter">
        <header style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px;">
            <div>
                <h1 style="font-size: 2rem; font-weight: 700; color: var(--slate-900);">Products</h1>
                <p style="color: var(--slate-400);">Manage inventory and stock levels.</p>
            </div>
            
            <a href="add_product.php" class="btn-primary">
                <i class='bx bx-plus'></i> Add New Product
            </a>
        </header>

        <div class="card-premium" style="padding: 0; overflow: hidden;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead style="background: rgba(255,255,255,0.05); border-bottom: 1px solid rgba(255,255,255,0.1);">
                    <tr>
                        <th style="padding: 20px; text-align: left; font-size: 12px; text-transform: uppercase; color: var(--slate-400);">Visual</th>
                        <th style="padding: 20px; text-align: left; font-size: 12px; text-transform: uppercase; color: var(--slate-400);">Product Name</th>
                        <th style="padding: 20px; text-align: left; font-size: 12px; text-transform: uppercase; color: var(--slate-400);">Type</th>
                        <th style="padding: 20px; text-align: left; font-size: 12px; text-transform: uppercase; color: var(--slate-400);">Price</th>
                        <th style="padding: 20px; text-align: left; font-size: 12px; text-transform: uppercase; color: var(--slate-400);">Stock</th>
                        <th style="padding: 20px; text-align: right; font-size: 12px; text-transform: uppercase; color: var(--slate-400);">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $result->fetch_assoc()): ?>
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.1);">
                        <td style="padding: 20px;">
                            <?php $img = !empty($row['image_url']) ? "../" . $row['image_url'] : "../uploads/default.png"; ?>
                            <img src="<?php echo $img; ?>" style="width: 48px; height: 48px; border-radius: 8px; object-fit: cover; border: 1px solid rgba(255,255,255,0.1);">
                        </td>
                        <td style="padding: 20px;">
                            <div style="font-weight: 700; color: white; font-size: 14px;"><?php echo $row['name']; ?></div>
                            <small style="color: var(--slate-400);">ID: #<?php echo $row['product_id']; ?></small>
                        </td>
                        <td style="padding: 20px;">
                            <span style="background: rgba(255,255,255,0.1); color: #cbd5e1; padding: 4px 12px; border-radius: 100px; font-size: 12px; font-weight: 600; text-transform: uppercase;"><?php echo $row['type']; ?></span>
                        </td>
                        <td style="padding: 20px; font-weight: 700; color: white;">₹<?php echo $row['price']; ?></td>
                        <td style="padding: 20px;">
                            <span style="font-weight: 700; <?php echo $row['stock_quantity'] > 0 ? 'color: var(--primary);' : 'color: #ef4444;'; ?>">
                                <?php echo $row['stock_quantity'] > 0 ? $row['stock_quantity'] . ' Units' : 'Out of Stock'; ?>
                            </span>
                        </td>
                        <td style="padding: 20px; text-align: right;">
                            <a href="edit_product.php?id=<?php echo $row['product_id']; ?>" style="color: var(--primary); margin-right: 12px; text-decoration: none; font-weight: 600;">Edit</a>
                            <a href="delete_product.php?id=<?php echo $row['product_id']; ?>" style="color: #ef4444; text-decoration: none; font-weight: 600;" onclick="return confirm('Initiate asset decommissioning?')">Delete</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>

