<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

include "../config.php";

$sql = "SELECT * FROM products ORDER BY product_id DESC";
$result = $conn->query($sql);

$products = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        // Ensure image URL is absolute or reachable
        // Ensure image URL is clean (remove ../ prefix if stored that way)
        $row['image_url'] = str_replace("../", "", $row['image_url']);
        $products[] = $row;
    }
}

echo json_encode([
    "status" => "success",
    "count" => count($products),
    "data" => $products
]);
?>
