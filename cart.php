<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
include "config.php";

// Handle Updates
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update'])) {
        foreach ($_POST['qty'] as $pid => $qty) {
            if ($qty <= 0) unset($_SESSION['cart'][$pid]);
            else $_SESSION['cart'][$pid] = $qty;
        }
    }
    if (isset($_POST['remove'])) {
        unset($_SESSION['cart'][$_POST['remove']]);
    }
}

$cart_items = [];
$total = 0;
if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    $ids = implode(',', array_keys($_SESSION['cart']));
    $sql = "SELECT * FROM products WHERE product_id IN ($ids)";
    $result = $conn->query($sql);
    if($result) {
        while ($row = $result->fetch_assoc()) {
            $row['qty'] = $_SESSION['cart'][$row['product_id']];
            $row['subtotal'] = $row['price'] * $row['qty'];
            $total += $row['subtotal'];
            $cart_items[] = $row;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart | FloraAI</title>
    <link rel="stylesheet" href="assests/css/global.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>

<div class="dashboard-container">
    <?php include "dashboard_sidebar.php"; ?>

    <main class="main-content animate-enter">
        <header style="margin-bottom: 40px;">
            <h1 style="font-size: 2rem; font-weight: 700; color: var(--slate-900);">Shopping Cart</h1>
            <p style="color: var(--slate-400);">Review selected items.</p>
        </header>

        <?php if(!empty($cart_items)): ?>
            <form method="POST">
                <div class="card-premium" style="padding: 0; overflow: hidden; margin-bottom: 32px;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead style="background: #f8fafc; border-bottom: 1px solid #f1f5f9; color: #1e293b;">
                            <tr>
                                <th style="padding: 20px; text-align: left; font-size: 12px; text-transform: uppercase;">Product</th>
                                <th style="padding: 20px; text-align: left; font-size: 12px; text-transform: uppercase;">Price</th>
                                <th style="padding: 20px; text-align: left; font-size: 12px; text-transform: uppercase;">Qty</th>
                                <th style="padding: 20px; text-align: right; font-size: 12px; text-transform: uppercase;">Total</th>
                                <th style="padding: 20px;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($cart_items as $item): ?>
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td style="padding: 20px; font-weight: 600;">
                                    <div style="display: flex; align-items: center; gap: 16px;">
                                        <img src="<?php echo $item['image_url']; ?>" style="width: 48px; height: 48px; border-radius: 8px; object-fit: cover;">
                                        <?php echo $item['name']; ?>
                                    </div>
                                </td>
                                <td style="padding: 20px; color: var(--slate-600);">₹<?php echo number_format($item['price'], 2); ?></td>
                                <td style="padding: 20px;">
                                    <input type="number" name="qty[<?php echo $item['product_id']; ?>]" value="<?php echo $item['qty']; ?>" min="0" style="width: 60px; padding: 8px; border: 1px solid #e2e8f0; border-radius: 8px; text-align: center;">
                                </td>
                                <td style="padding: 20px; text-align: right; font-weight: 700;">₹<?php echo number_format($item['subtotal'], 2); ?></td>
                                <td style="padding: 20px; text-align: right;">
                                    <button type="submit" name="remove" value="<?php echo $item['product_id']; ?>" onclick="return confirm('Are you sure you want to remove this item?');" style="color: #ef4444; background: none; border: none; cursor: pointer; font-size: 20px;"><i class='bx bx-trash'></i></button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div style="display: flex; justify-content: flex-end; align-items: center; gap: 32px;">
                    <button type="submit" name="update" style="background: none; border: none; color: var(--slate-600); cursor: pointer; text-decoration: underline;">Update Cart</button>
                    <div style="text-align: right;">
                        <span style="display: block; font-size: 12px; text-transform: uppercase; color: var(--slate-400); font-weight: 700;">Total</span>
                        <span style="font-size: 2rem; font-weight: 800; color: var(--slate-900);">₹<?php echo number_format($total, 2); ?></span>
                    </div>
                    <a href="checkout.php" class="btn-primary" style="padding: 16px 40px; font-size: 16px;">Checkout</a>
                </div>
            </form>
        <?php else: ?>
            <p>Your cart is currently empty.</p>
        <?php endif; ?>
    </main>
</div>

<style>
    @media (max-width: 768px) {
        table { display: block; overflow-x: auto; }
    }
</style>

</body>
</html>
