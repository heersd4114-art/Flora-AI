<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
include "config.php";

// Filter Logic
$type = isset($_GET['type']) ? $_GET['type'] : 'All';
$where = "";
if ($type != 'All') {
    $where = "WHERE type = '$type'";
}

// Search Logic
if (isset($_GET['search'])) {
    $search = $_GET['search'];
    $where = ($where == "") ? "WHERE name LIKE '%$search%'" : "$where AND name LIKE '%$search%'";
}

$sql = "SELECT * FROM products $where ORDER BY product_id DESC";
$result = $conn->query($sql);

$cart_count = 0;
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $qty) {
        $cart_count += $qty;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Marketplace | FloraAI</title>
    <link rel="stylesheet" href="assests/css/global.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>

<div class="dashboard-container">
    <?php include "dashboard_sidebar.php"; ?>

    <main class="main-content animate-enter">
        <header style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px; flex-wrap: wrap; gap: 20px;">
            <div>
                <h1 style="font-size: 2rem; font-weight: 700; color: var(--slate-900);">Marketplace</h1>
                <p style="color: var(--slate-400);">Premium botanical supplies.</p>
            </div>
            
            <form method="GET" style="display: flex; gap: 10px;">
                <input type="text" name="search" placeholder="Search..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>" style="padding: 10px 16px; border: 1px solid #e2e8f0; border-radius: 8px; width: 250px;">
                <button type="submit" class="btn-primary"><i class='bx bx-search'></i></button>
            </form>
        </header>

        <div style="margin-bottom: 32px; display: flex; gap: 10px; overflow-x: auto; padding-bottom: 8px;">
            <a href="store.php?type=All" class="btn-primary" style="background: <?php echo $type == 'All' ? 'var(--primary)' : 'white'; ?>; color: <?php echo $type == 'All' ? 'white' : '#1e293b'; ?>; border: 1px solid <?php echo $type == 'All' ? 'none' : '#e2e8f0'; ?>;">All</a>
            <a href="store.php?type=Pesticide" class="btn-primary" style="background: <?php echo $type == 'Pesticide' ? 'var(--primary)' : 'white'; ?>; color: <?php echo $type == 'Pesticide' ? 'white' : '#1e293b'; ?>; border: 1px solid <?php echo $type == 'Pesticide' ? 'none' : '#e2e8f0'; ?>;">Pesticides</a>
            <a href="store.php?type=Fertilizer" class="btn-primary" style="background: <?php echo $type == 'Fertilizer' ? 'var(--primary)' : 'white'; ?>; color: <?php echo $type == 'Fertilizer' ? 'white' : '#1e293b'; ?>; border: 1px solid <?php echo $type == 'Fertilizer' ? 'none' : '#e2e8f0'; ?>;">Fertilizers</a>
            <a href="store.php?type=Tool" class="btn-primary" style="background: <?php echo $type == 'Tool' ? 'var(--primary)' : 'white'; ?>; color: <?php echo $type == 'Tool' ? 'white' : '#1e293b'; ?>; border: 1px solid <?php echo $type == 'Tool' ? 'none' : '#e2e8f0'; ?>;">Tools</a>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 24px;">
            <?php if ($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                <div class="card-premium" style="padding: 0; overflow: hidden; display: flex; flex-direction: column;">
                    <div style="height: 200px; overflow: hidden;">
                        <img src="<?php echo !empty($row['image_url']) ? $row['image_url'] : 'uploads/default.png'; ?>" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                    <div style="padding: 24px; flex: 1; display: flex; flex-direction: column;">
                        <span style="font-size: 11px; text-transform: uppercase; color: var(--slate-400); font-weight: 700; margin-bottom: 8px;"><?php echo $row['type']; ?></span>
                        <h3 style="font-size: 1.1rem; color: var(--slate-900); margin-bottom: 8px;"><?php echo $row['name']; ?></h3>
                        <p style="font-size: 13px; color: var(--slate-600); margin-bottom: 20px; flex: 1;"><?php echo $row['description']; ?></p>
                        
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="font-size: 1.25rem; font-weight: 800; color: var(--primary);">₹<?php echo $row['price']; ?></span>
                            <?php if($row['stock_quantity'] > 0): ?>
                                <div style="display: flex; gap: 8px;">
                                    <a href="#" onclick="return addAjaxToCart(event, <?php echo $row['product_id']; ?>);" class="btn-primary" style="padding: 8px 12px;" title="Add to Cart"><i class='bx bx-plus'></i></a>
                                    <a href="add_to_cart.php?id=<?php echo $row['product_id']; ?>&redirect=cart" class="btn-primary" style="padding: 8px 12px; background: var(--accent); box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);" title="Buy Now"><i class='bx bx-cart-add'></i></a>
                                </div>
                            <?php else: ?>
                                <span style="font-weight: 700; color: #ef4444; font-size: 12px;">OUT OF STOCK</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No products found matching your criteria.</p>
            <?php endif; ?>
        </div>
    </main>
</div>

</body>
</html>
