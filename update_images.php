<?php
include "config.php";

$updates = [
    'Organic Neem Oil Extract' => 'assests/images/product_1.png',
    'Copper Fungicide Spray' => 'assests/images/product_2.png',
    'All-Purpose Liquid Plant Food' => 'assests/images/product_3.png',
    'Slow-Release Fertilizer Pellets' => 'assests/images/product_4.png',
    'Premium Pruning Shears' => 'assests/images/product_5.png',
    'Soil Moisture Meter' => 'assests/images/product_6.png',
    'Insecticidal Soap Spray' => 'assests/images/product_7.png',
    'Premium Potting Mix' => 'assests/images/product_8.png'
];

foreach ($updates as $name => $img) {
    if ($stmt = $conn->prepare("UPDATE products SET image_url = ? WHERE name = ?")) {
        $stmt->bind_param("ss", $img, $name);
        $stmt->execute();
        echo "Updated $name -> $img<br>";
    }
}
echo "<strong>Successfully Updated All Custom AI Images!</strong>";
?>
