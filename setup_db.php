<?php
include "config.php";

$sql_users = "CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(100) UNIQUE,
    password VARCHAR(255),
    role VARCHAR(20) DEFAULT 'user'
)";

$sql_products = "CREATE TABLE IF NOT EXISTS products (
    product_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    description TEXT,
    price DECIMAL(10,2),
    image_url VARCHAR(255),
    category VARCHAR(50)
)";

$sql_cart = "CREATE TABLE IF NOT EXISTS cart (
    cart_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    product_id INT,
    quantity INT DEFAULT 1,
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (product_id) REFERENCES products(product_id)
)";

$sql_orders = "CREATE TABLE IF NOT EXISTS orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    total_amount DECIMAL(10,2),
    status VARCHAR(50) DEFAULT 'Pending',
    payment_method VARCHAR(50) DEFAULT 'COD',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id)
)";

$sql_diseases = "CREATE TABLE IF NOT EXISTS diseases (
    disease_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    description TEXT
)";

if ($conn->query($sql_users) === TRUE) echo "Users table ready.<br>"; else echo "Error Users: " . $conn->error . "<br>";
if ($conn->query($sql_products) === TRUE) echo "Products table ready.<br>"; else echo "Error Products: " . $conn->error . "<br>";
if ($conn->query($sql_cart) === TRUE) echo "Cart table ready.<br>"; else echo "Error Cart: " . $conn->error . "<br>";
if ($conn->query($sql_orders) === TRUE) echo "Orders table ready.<br>"; else echo "Error Orders: " . $conn->error . "<br>";
if ($conn->query($sql_diseases) === TRUE) echo "Diseases table ready.<br>"; else echo "Error Diseases: " . $conn->error . "<br>";

echo "Database Setup Complete.";
?>
