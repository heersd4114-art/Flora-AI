<?php
session_start();
include "../config.php";

// Fetch Categories for Dropdown
$cats = $conn->query("SELECT * FROM categories");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $sci_name = $_POST['scientific_name'];
    $desc = $_POST['description'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $category_id = $_POST['category_id'];
    $care = $_POST['care_level'];
    
    // Image Upload Logic
    $target_dir = "../uploads/";
    
    // Ensure uploads directory exists
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $image_url = "";
    
    if (isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {
        $filename = uniqid() . "_" . basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $filename;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if($check !== false) {
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                $image_url = "uploads/" . $filename; // Store relative path for DB
            } else {
                $error = "Sorry, there was an error uploading your file.";
            }
        } else {
            $error = "File is not an image.";
        }
    }

    if (!isset($error)) {
        $stmt = $conn->prepare("INSERT INTO plants (name, scientific_name, description, price, stock_quantity, category_id, care_level, image_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssdisss", $name, $sci_name, $desc, $price, $stock, $category_id, $care, $image_url);
        
        if ($stmt->execute()) {
            header("Location: plants.php");
            exit;
        } else {
            $error = "Database Error: " . $conn->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Add Plant</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    <?php include "includes/sidebar.php"; ?>
    
    <section class="home-section">
        <div class="text">Add New Plant</div>
        
        <div class="form-container">
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Plant Name</label>
                    <input type="text" name="name" required>
                </div>
                
                <div class="form-group">
                    <label>Scientific Name</label>
                    <input type="text" name="scientific_name">
                </div>
                
                <div class="form-group">
                    <label>Category</label>
                    <select name="category_id" required>
                        <option value="">Select Category</option>
                        <?php while($c = $cats->fetch_assoc()): ?>
                            <option value="<?php echo $c['category_id']; ?>"><?php echo $c['category_name']; ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                
                <div class="row">
                    <div class="form-group">
                        <label>Price ($)</label>
                        <input type="number" step="0.01" name="price" required>
                    </div>
                    <div class="form-group">
                        <label>Stock Quantity</label>
                        <input type="number" name="stock" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Care Level</label>
                    <select name="care_level">
                        <option value="Easy">Easy</option>
                        <option value="Medium">Medium</option>
                        <option value="Hard">Hard</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" rows="4"></textarea>
                </div>
                
                <div class="form-group">
                    <label>Plant Image</label>
                    <input type="file" name="image" accept="image/*" required>
                </div>
                
                <?php if(isset($error)): ?>
                    <div class="error-msg"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <button type="submit" class="btn-save">Save Plant</button>
            </form>
        </div>

    </section>

    <style>
        .form-container {
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            max-width: 600px;
            margin: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            color: #333;
        }
        input, select, textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }
        .row {
            display: flex;
            gap: 15px;
        }
        .row .form-group {
            flex: 1;
        }
        .btn-save {
            background: #2e7d32;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
        }
        .error-msg {
            color: red;
            background: #ffebee;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
        }
    </style>
</body>
</html>
