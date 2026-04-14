<?php
session_start();
include "../config.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit;
}

// Fetch Plants
$sql = "SELECT p.*, c.category_name FROM plants p LEFT JOIN categories c ON p.category_id = c.category_id ORDER BY p.plant_id DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Manage Plants</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    <?php include "includes/sidebar.php"; ?>
    
    <section class="home-section">
        <div class="header">
            <div class="text">Manage Plants</div>
            <a href="add_plant.php" class="btn-add"><i class='bx bx-plus'></i> Add New Plant</a>
        </div>
        
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td>
                            <?php 
                                $img = !empty($row['image_url']) ? "../" . $row['image_url'] : "../uploads/default_plant.png"; 
                            ?>
                            <img src="<?php echo $img; ?>" alt="Plant" class="plant-img">
                        </td>
                        <td>
                            <strong><?php echo $row['name']; ?></strong><br>
                            <small><?php echo $row['scientific_name']; ?></small>
                        </td>
                        <td><?php echo $row['category_name']; ?></td>
                        <td>$<?php echo $row['price']; ?></td>
                        <td>
                            <span class="badge <?php echo $row['stock_quantity'] > 0 ? 'instock' : 'outstock'; ?>">
                                <?php echo $row['stock_quantity']; ?>
                            </span>
                        </td>
                        <td>
                            <a href="edit_plant.php?id=<?php echo $row['plant_id']; ?>" class="btn-action edit"><i class='bx bx-edit-alt'></i></a>
                            <a href="delete_plant.php?id=<?php echo $row['plant_id']; ?>" class="btn-action delete" onclick="return confirm('Are you sure?')"><i class='bx bx-trash'></i></a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </section>

    <style>
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-right: 20px;
        }
        .btn-add {
            background: #2e7d32;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .table-container {
            padding: 20px;
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }
        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        th {
            background: #f8f9fa;
            font-weight: 600;
        }
        .plant-img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 5px;
        }
        .badge {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: bold;
        }
        .badge.instock { background: #e8f5e9; color: #2e7d32; }
        .badge.outstock { background: #ffebee; color: #c62828; }
        
        .btn-action {
            padding: 5px 10px;
            border-radius: 5px;
            color: white;
            text-decoration: none;
            margin-right: 5px;
        }
        .edit { background: #ffa000; }
        .delete { background: #d32f2f; }
    </style>
</body>
</html>
