<?php
session_start();
include "config.php";

// 1. Check Login
if (!isset($_SESSION['user_id'])) {
    // Redirect to login, but remember we want to come back here
    $_SESSION['redirect_to'] = 'checkout.php';
    header("Location: login.php");
    exit;
}

// 2. Check Empty Cart
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("Location: store.php");
    exit;
}

// 3. Process Order
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $street = $_POST['street'];
    $city = $_POST['city'];
    $zip = $_POST['zip'];
    $phone = $_POST['phone'];
    $address = $street . ", " . $city . " - " . $zip . " (Ph: " . $phone . ")";
    $payment = "COD"; // Default for now
    $total = 0;

    // Calculate Total & Validate Stock
    $cart_data = [];
    $ids = implode(',', array_keys($_SESSION['cart']));
    $result = $conn->query("SELECT * FROM products WHERE product_id IN ($ids)");
    while($row = $result->fetch_assoc()) {
        $qty = $_SESSION['cart'][$row['product_id']];
        $total += $row['price'] * $qty;
        $cart_data[] = ['id' => $row['product_id'], 'qty' => $qty, 'price' => $row['price']];
    }

    // Insert Order
    $stmt = $conn->prepare("INSERT INTO orders (user_id, total_amount, shipping_address, payment_method, street, city, zip, phone) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("idssssss", $user_id, $total, $address, $payment, $street, $city, $zip, $phone);
    
    if ($stmt->execute()) {
        $order_id = $conn->insert_id;

        // Insert Order Items
        $stmt_item = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price_at_purchase) VALUES (?, ?, ?, ?)");
        foreach ($cart_data as $item) {
            $stmt_item->bind_param("iiid", $order_id, $item['id'], $item['qty'], $item['price']);
            $stmt_item->execute();
            
            // Reduce Stock (Optional but recommended)
            $conn->query("UPDATE products SET stock_quantity = stock_quantity - {$item['qty']} WHERE product_id = {$item['id']}");
        }

        // Assign Delivery Partner (Simple Round Robin or Random for now)
        // In real app, Admin assigns it manually or based on location
        $partner = $conn->query("SELECT partner_id FROM delivery_partners WHERE status='Available' LIMIT 1")->fetch_assoc();
        if ($partner) {
            $pid = $partner['partner_id'];
            $conn->query("INSERT INTO shipments (order_id, partner_id, status) VALUES ($order_id, $pid, 'Pending')");
        }

        // Clear Cart
        unset($_SESSION['cart']);
        
        // Redirect to Success
        echo "<script>alert('Order Placed Successfully! Order ID: #$order_id'); window.location='orders.php';</script>";
        exit;
    } else {
        $error = "Order Failed: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Checkout</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f9f9f9; color: #1e293b !important; padding-bottom: 50px; }
        .container { max-width: 600px; margin: 30px auto; padding: 0 20px; }
        
        .checkout-box {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }
        
        h2 { color: #2e7d32; margin-top: 0; }
        
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        textarea, select { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; }
        
        .summary {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .btn-place {
            background: #2e7d32;
            color: white;
            padding: 15px;
            border: none;
            width: 100%;
            border-radius: 8px;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <?php include "includes/navbar.php"; ?>
    
    <div class="container">
        <div class="checkout-box">
            <h2>Checkout</h2>
            
            <div class="summary">
                <p><strong>Review Your Order</strong></p>
                <p>Total Items: <?php echo count($_SESSION['cart']); ?></p>
            </div>
            
            <form method="POST">
                <div class="form-group">
                    <label>Street Address</label>
                    <input type="text" name="street" required placeholder="123 Main St" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                </div>
                <div class="form-group">
                    <label>City</label>
                    <input type="text" name="city" required placeholder="City Name" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                </div>
                <div class="form-group">
                    <label>ZIP / Postal Code</label>
                    <input type="text" name="zip" required placeholder="XYZ 123" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                </div>
                <div class="form-group">
                    <label>Phone Number</label>
                    <input type="text" name="phone" required placeholder="+1 234 567 8900" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                </div>
                
                <div class="form-group">
                    <label>Payment Method</label>
                    <select name="payment">
                        <option value="COD">Cash on Delivery (COD)</option>
                    </select>
                </div>
                
                <button type="submit" class="btn-place">Place Order</button>
            </form>
        </div>
    </div>

</body>
</html>
