<?php
include "config.php";

$keras_classes = [
    "Apple scab" => 5, // Copper Fungicide
    "Black rot" => 5,
    "Cedar apple rust" => 5,
    "Powdery mildew" => 5,
    "Cercospora leaf spot Gray leaf spot" => 5,
    "Common rust" => 5,
    "Northern Leaf Blight" => 5,
    "Esca (Black Measles)" => 5,
    "Leaf blight (Isariopsis Leaf Spot)" => 5,
    "Haunglongbing (Citrus greening)" => 7, // Pruning Shears (Bacteria - no cure)
    "Bacterial spot" => 5, # Copper Fungicide works on some bacteria
    "Early blight" => 5,
    "Late blight" => 5,
    "Leaf scorch" => 5,
    "Leaf Mold" => 5,
    "Septoria leaf spot" => 5,
    "Spider mites Two-spotted spider mite" => 1, // Neem Oil
    "Target Spot" => 5,
    "Tomato Yellow Leaf Curl Virus" => 7, // Pruning Shears (Virus)
    "Tomato mosaic virus" => 7, // Pruning shears
    "Healthy" => 6 // Fertilizer
];

foreach ($keras_classes as $disease_name => $product_id) {
    // Check if disease exists
    $stmt = $conn->prepare("SELECT disease_id FROM diseases WHERE name = ?");
    $stmt->bind_param("s", $disease_name);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 0) {
        // Insert disease
        $stmt_insert = $conn->prepare("INSERT INTO diseases (name, symptoms, care_tips) VALUES (?, '', '')");
        $stmt_insert->bind_param("s", $disease_name);
        $stmt_insert->execute();
        $disease_id = $conn->insert_id;
        echo "Inserted disease: $disease_name (ID: $disease_id)\n";
    } else {
        $row = $result->fetch_assoc();
        $disease_id = $row['disease_id'];
    }
    
    // Check if mapping exists
    $stmt_map = $conn->prepare("SELECT id FROM disease_treatments WHERE disease_id = ? AND product_id = ?");
    $stmt_map->bind_param("ii", $disease_id, $product_id);
    $stmt_map->execute();
    $res_map = $stmt_map->get_result();
    
    if ($res_map->num_rows == 0) {
        // Insert mapping
        $stmt_insert_map = $conn->prepare("INSERT INTO disease_treatments (disease_id, product_id) VALUES (?, ?)");
        $stmt_insert_map->bind_param("ii", $disease_id, $product_id);
        $stmt_insert_map->execute();
        echo "Mapped $disease_name to product $product_id\n";
    }
}

echo "Database update complete.\n";
?>
