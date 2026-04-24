<?php
include "config.php";

echo "Starting Store Seed process...<br>";

// 1. Array of New Products
$products = [
    [
        'name' => 'Organic Neem Oil Extract',
        'type' => 'Pesticide',
        'description' => '100% cold-pressed organic neem oil. The ultimate natural solution for repelling aphids, spider mites, mealybugs, and whiteflies while keeping your plants safe.',
        'price' => 12.99,
        'stock' => 50,
        'image' => 'https://images.unsplash.com/photo-1621508654686-809f23efdabc?q=80&w=400&auto=format&fit=crop'
    ],
    [
        'name' => 'Copper Fungicide Spray',
        'type' => 'Pesticide',
        'description' => 'High-strength copper-based fungicide to quickly halt the spread of leaf blight, powdery mildew, rust, and leaf spots on contact.',
        'price' => 15.49,
        'stock' => 30,
        'image' => 'https://images.unsplash.com/photo-1590682680695-43b964a3ae17?q=80&w=400&auto=format&fit=crop'
    ],
    [
        'name' => 'All-Purpose Liquid Plant Food',
        'type' => 'Fertilizer',
        'description' => 'A balanced 10-10-10 liquid fertilizer packed with micronutrients to instantly cure nutrient deficiencies and restore yellowing leaves.',
        'price' => 18.00,
        'stock' => 100,
        'image' => 'https://images.unsplash.com/photo-1622383563227-04401ab4e5ea?q=80&w=400&auto=format&fit=crop'
    ],
    [
        'name' => 'Slow-Release Fertilizer Pellets',
        'type' => 'Fertilizer',
        'description' => 'Feeds your plants continuously for up to 6 months. Perfect for maintaining healthy growth and preventing future deficiencies.',
        'price' => 22.99,
        'stock' => 45,
        'image' => 'https://images.unsplash.com/photo-1591857177580-dc82b9ac4e1e?q=80&w=400&auto=format&fit=crop'
    ],
    [
        'name' => 'Premium Pruning Shears',
        'type' => 'Tool',
        'description' => 'Razor-sharp stainless steel bypass pruning shears. Essential for safely removing scorched, infected, or dying leaves without damaging the stem.',
        'price' => 24.50,
        'stock' => 25,
        'image' => 'https://images.unsplash.com/photo-1416879598446-f28a38ae42ae?q=80&w=400&auto=format&fit=crop'
    ],
    [
        'name' => 'Soil Moisture Meter',
        'type' => 'Tool',
        'description' => 'Instantly detect if your plant needs water or is at risk of root rot. Takes the guesswork out of watering.',
        'price' => 11.25,
        'stock' => 80,
        'image' => 'https://images.unsplash.com/photo-1585320806297-9794b3e4aa88?q=80&w=400&auto=format&fit=crop'
    ],
    [
        'name' => 'Insecticidal Soap Spray',
        'type' => 'Pesticide',
        'description' => 'Gentle but highly effective soap spray for immediate knockdown of soft-bodied insects like aphids and spider mites.',
        'price' => 9.99,
        'stock' => 60,
        'image' => 'https://images.unsplash.com/photo-1628352081506-83c43123ed6d?q=80&w=400&auto=format&fit=crop'
    ],
    [
        'name' => 'Premium Potting Mix',
        'type' => 'Other',
        'description' => 'Well-draining, nutrient-rich soil mix specifically designed to prevent root rot and encourage rapid root development. Contains perlite and worm castings.',
        'price' => 16.50,
        'stock' => 40,
        'image' => 'https://images.unsplash.com/photo-1487701161594-b14e21dc0752?q=80&w=400&auto=format&fit=crop'
    ]
];

// Insert Products and map ID
$product_ids = [];
foreach ($products as $p) {
    // Check if exists
    $stmt = $conn->prepare("SELECT product_id FROM products WHERE name = ?");
    $stmt->bind_param("s", $p['name']);
    $stmt->execute();
    $res = $stmt->get_result();
    
    if ($res->num_rows == 0) {
        $stmt_i = $conn->prepare("INSERT INTO products (name, type, description, price, stock_quantity, image_url) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt_i->bind_param("sssdis", $p['name'], $p['type'], $p['description'], $p['price'], $p['stock'], $p['image']);
        $stmt_i->execute();
        $product_ids[$p['name']] = $conn->insert_id;
        echo "Inserted Product: {$p['name']}<br>";
    } else {
        $row = $res->fetch_assoc();
        $product_ids[$p['name']] = $row['product_id'];
    }
}

// 2. Map Gemini Disease Terms to Products
$disease_mappings = [
    'Aphid Infestation' => ['Organic Neem Oil Extract', 'Insecticidal Soap Spray'],
    'Spider mites' => ['Organic Neem Oil Extract', 'Insecticidal Soap Spray'],
    'Whiteflies' => ['Organic Neem Oil Extract'],
    'Mealybugs' => ['Organic Neem Oil Extract', 'Insecticidal Soap Spray'],
    'Leaf Blight' => ['Copper Fungicide Spray', 'Premium Pruning Shears'],
    'Leaf Spot' => ['Copper Fungicide Spray', 'Premium Pruning Shears'],
    'Powdery Mildew' => ['Copper Fungicide Spray', 'Organic Neem Oil Extract'],
    'Downy Mildew' => ['Copper Fungicide Spray'],
    'Rust' => ['Copper Fungicide Spray', 'Premium Pruning Shears'],
    'Late blight' => ['Copper Fungicide Spray'],
    'Early blight' => ['Copper Fungicide Spray'],
    'Nutrient Deficiency' => ['All-Purpose Liquid Plant Food', 'Slow-Release Fertilizer Pellets'],
    'Root Rot' => ['Premium Potting Mix', 'Soil Moisture Meter', 'Premium Pruning Shears'],
    'Leaf Scorch' => ['Premium Pruning Shears', 'Soil Moisture Meter'],
    'Overwatering' => ['Soil Moisture Meter', 'Premium Potting Mix'],
    'Underwatering' => ['Soil Moisture Meter', 'All-Purpose Liquid Plant Food'],
    'Healthy' => ['Slow-Release Fertilizer Pellets', 'Soil Moisture Meter'],
    'Needs Monitoring' => ['Soil Moisture Meter', 'Organic Neem Oil Extract']
];

foreach ($disease_mappings as $disease_name => $recommended_products) {
    // Check if disease exists or insert
    $stmt = $conn->prepare("SELECT disease_id FROM diseases WHERE name = ?");
    $stmt->bind_param("s", $disease_name);
    $stmt->execute();
    $res = $stmt->get_result();
    
    if ($res->num_rows == 0) {
        $ins = $conn->prepare("INSERT INTO diseases (name, symptoms, care_tips) VALUES (?, 'Identified by AI', 'Follow AI Treatment Steps')");
        $ins->bind_param("s", $disease_name);
        $ins->execute();
        $disease_id = $conn->insert_id;
        echo "Added Disease Category: {$disease_name}<br>";
    } else {
        $row = $res->fetch_assoc();
        $disease_id = $row['disease_id'];
    }
    
    // Map to products
    foreach ($recommended_products as $prod_name) {
        if (isset($product_ids[$prod_name])) {
            $pid = $product_ids[$prod_name];
            
            // Check mapping
            $chk = $conn->prepare("SELECT id FROM disease_treatments WHERE disease_id = ? AND product_id = ?");
            $chk->bind_param("ii", $disease_id, $pid);
            $chk->execute();
            if ($chk->get_result()->num_rows == 0) {
                $ins_map = $conn->prepare("INSERT INTO disease_treatments (disease_id, product_id) VALUES (?, ?)");
                $ins_map->bind_param("ii", $disease_id, $pid);
                $ins_map->execute();
                echo "Mapped '{$prod_name}' to '{$disease_name}'<br>";
            }
        }
    }
}

echo "<strong>Store Seed Complete! Database is populated with new items and disease connections.</strong>";
?>
