<?php
session_start();

if (isset($_GET['id'])) {
    $product_id = $_GET['id'];
    
    // Initialize cart if not exists
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Add or Increment
    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id]++;
    } else {
        $_SESSION['cart'][$product_id] = 1;
    }
}

if (isset($_GET['ajax']) && $_GET['ajax'] == 'true') {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'success']);
    exit;
}

// Redirect logic
$redirect_url = "store.php";
if (isset($_GET['redirect']) && $_GET['redirect'] == 'cart') {
    $redirect_url = "cart.php";
} elseif(isset($_SERVER['HTTP_REFERER'])) {
    $redirect_url = $_SERVER['HTTP_REFERER'];
}

echo "<script>
    window.location.href = '$redirect_url';
</script>";
exit;
