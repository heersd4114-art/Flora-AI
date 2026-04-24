<?php
include "config.php";

echo "<h2>Database Check</h2>";

// Check Users Table
echo "<h3>Users Table Columns:</h3>";
$result = $conn->query("DESCRIBE users");
if ($result) {
    while($row = $result->fetch_assoc()) {
        echo $row['Field'] . " - " . $row['Type'] . "<br>";
    }
} else {
    echo "USERS table missing!<br>";
}

// Check Cart Table
echo "<h3>Cart Table Columns:</h3>";
$result = $conn->query("DESCRIBE cart");
if ($result) {
    while($row = $result->fetch_assoc()) {
        echo $row['Field'] . " - " . $row['Type'] . "<br>";
    }
} else {
    echo "CART table missing!<br>";
}

// Check Products Table
echo "<h3>Products Table Columns:</h3>";
$result = $conn->query("DESCRIBE products");
if ($result) {
    while($row = $result->fetch_assoc()) {
        echo $row['Field'] . " - " . $row['Type'] . "<br>";
    }
} else {
    echo "PRODUCTS table missing!<br>";
}
?>
