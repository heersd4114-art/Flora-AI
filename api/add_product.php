<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");

include "../config.php";

$name = $_POST['name'];
$description = $_POST['description'];
$price = $_POST['price'];
$stock = 100; // Default
$category_id = 1; // Default

// Image Upload
$target_dir = "../uploads/products/";
if (!file_exists($target_dir)) mkdir($target_dir, 0777, true);

$image_name = time() . "_" . basename($_FILES["image"]["name"]);
$target_file = $target_dir . $image_name;
$db_image_path = "uploads/products/" . $image_name;

if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
    $stmt = $conn->prepare("INSERT INTO products (name, description, price, stock, category_id, image_url) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdiis", $name, $description, $price, $stock, $category_id, $db_image_path);
    
    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Product created"]);
    } else {
        echo json_encode(["status" => "error", "message" => "DB Insert failed"]);
    }
} else {
        echo json_encode(["status" => "error", "message" => "Image upload failed"]);
}
?>
