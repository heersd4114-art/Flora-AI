<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");

include "../config.php";

$data = json_decode(file_get_contents("php://input"), true);
$action = $data['action'] ?? '';

if (!$action) {
    echo json_encode(["status" => "error", "message" => "No action specified"]);
    exit;
}

$user_id = $data['user_id'] ?? 0;
if ($user_id == 0) {
    echo json_encode(["status" => "error", "message" => "User ID required"]);
    exit;
}

// 1. ADD TO CART
if ($action == 'add') {
    $product_id = $data['product_id'];
    $quantity = $data['quantity'] ?? 1;

    // Check if exists
    $stmt = $conn->prepare("SELECT cart_id, quantity FROM cart WHERE user_id = ? AND product_id = ?");
    $stmt->bind_param("ii", $user_id, $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Update Quantity
        $row = $result->fetch_assoc();
        $new_qty = $row['quantity'] + $quantity;
        $update = $conn->prepare("UPDATE cart SET quantity = ? WHERE cart_id = ?");
        $update->bind_param("ii", $new_qty, $row['cart_id']);
        $update->execute();
    } else {
        // Insert New
        $insert = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
        $insert->bind_param("iii", $user_id, $product_id, $quantity);
        $insert->execute();
    }
    echo json_encode(["status" => "success", "message" => "Item added to cart"]);
}

// 2. VIEW CART
if ($action == 'view') {
    $stmt = $conn->prepare("SELECT c.cart_id, c.quantity, p.product_id, p.name, p.price, p.image_url 
                            FROM cart c 
                            JOIN products p ON c.product_id = p.product_id 
                            WHERE c.user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $items = [];
    $total = 0;
    while ($row = $result->fetch_assoc()) {
        $row['subtotal'] = $row['price'] * $row['quantity'];
        $total += $row['subtotal'];
        $items[] = $row;
    }

    echo json_encode(["status" => "success", "data" => $items, "total" => $total]);
}

// 3. REMOVE ITEM
if ($action == 'remove') {
    $cart_id = $data['cart_id'];
    $stmt = $conn->prepare("DELETE FROM cart WHERE cart_id = ? AND user_id = ?");
    $stmt->bind_param("ii", $cart_id, $user_id);
    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Item removed"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to remove"]);
    }
}

// 4. CHECKOUT (Create Order from Cart)
if ($action == 'checkout') {
    // Calculate Total
    $stmt = $conn->prepare("SELECT c.quantity, p.price FROM cart c JOIN products p ON c.product_id = p.product_id WHERE c.user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $total_amount = 0;
    while ($row = $result->fetch_assoc()) {
        $total_amount += ($row['price'] * $row['quantity']);
    }

    if ($total_amount > 0) {
        // Create Order
        $street = $data['street'] ?? '';
        $city = $data['city'] ?? '';
        $zip = $data['zip'] ?? '';
        $phone = $data['phone'] ?? '';
        $status = 'Pending';
        $payment = 'COD';
        $insert_order = $conn->prepare("INSERT INTO orders (user_id, total_amount, status, payment_method, street, city, zip, phone) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $insert_order->bind_param("idssssss", $user_id, $total_amount, $status, $payment, $street, $city, $zip, $phone);
        
        if ($insert_order->execute()) {
            // Clear Cart
            $clear_cart = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
            $clear_cart->bind_param("i", $user_id);
            $clear_cart->execute();
            
            echo json_encode(["status" => "success", "message" => "Order placed successfully!"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to create order"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Cart is empty"]);
    }
}
?>
