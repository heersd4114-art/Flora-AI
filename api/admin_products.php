<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET");

include "../config.php";

$method = $_SERVER['REQUEST_METHOD'];

// Handle GET (List Products)
if ($method == 'GET') {
    $sql = "SELECT * FROM products ORDER BY created_at DESC";
    $result = $conn->query($sql);
    $products = [];
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
    echo json_encode(["status" => "success", "data" => $products]);
    exit;
}

// Handle POST (Add or Delete)
if ($method == 'POST') {
    // Check if it's a delete request (JSON body) or Add request (Multipart)
    // Note: React Native FormData sends Multipart.
    
    // DELETE
    $input = json_decode(file_get_contents("php://input"), true);
    if (isset($input['action']) && $input['action'] == 'delete') {
        $id = $input['product_id'];
        $stmt = $conn->prepare("DELETE FROM products WHERE product_id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Product deleted"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Delete failed"]);
        }
        exit;
    }

    // UPDATE PRODUCT
    if (isset($_POST['action']) && $_POST['action'] == 'update') {
        $id = $_POST['product_id'];
        $name = $_POST['name'];
        $description = $_POST['description'];
        $price = $_POST['price'];
        $stock = $_POST['stock'];
        
        // Handle Image if new one uploaded
        if (isset($_FILES['image']) && $_FILES['image']['size'] > 0) {
            $target_dir = "../uploads/products/";
            $image_name = time() . "_" . basename($_FILES["image"]["name"]);
            $target_file = $target_dir . $image_name;
            $db_image_path = "uploads/products/" . $image_name;
            
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                $stmt = $conn->prepare("UPDATE products SET name=?, description=?, price=?, stock=?, image_url=? WHERE product_id=?");
                $stmt->bind_param("ssdisi", $name, $description, $price, $stock, $db_image_path, $id);
            }
        } else {
            // No new image, keep old
            $stmt = $conn->prepare("UPDATE products SET name=?, description=?, price=?, stock=? WHERE product_id=?");
            $stmt->bind_param("ssdii", $name, $description, $price, $stock, $id);
        }

        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Product updated"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Update failed"]);
        }
        exit;
    }

    // ADD PRODUCT (Only if not updated)
    elseif (isset($_POST['name'])) {
        $name = $_POST['name'];
        $description = $_POST['description'];
        $price = $_POST['price'];
        $stock = $_POST['stock'];
        $category_id = 1; // Default for now

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
        exit;
    }
}
?>
