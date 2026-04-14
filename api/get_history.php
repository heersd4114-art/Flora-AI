<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

include "../config.php";

$inputJSON = file_get_contents('php://input');
$input = json_decode($inputJSON, TRUE);

$user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : (isset($input['user_id']) ? intval($input['user_id']) : 0);

if ($user_id > 0) {
    $sql = "SELECT ph.*, d.disease_id, p.product_id, p.name as product_name, p.price, p.image_url as product_image 
            FROM plant_history ph
            LEFT JOIN diseases d ON ph.disease_detected = d.name
            LEFT JOIN disease_treatments dt ON d.disease_id = dt.disease_id
            LEFT JOIN products p ON dt.product_id = p.product_id
            WHERE ph.user_id = ? 
            ORDER BY ph.scan_date DESC";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $history_map = [];
    while ($row = $result->fetch_assoc()) {
        $id = $row['id'];
        if (!isset($history_map[$id])) {
            $history_map[$id] = $row;
            $history_map[$id]['cures'] = [];
        }
        if ($row['product_id']) {
            $history_map[$id]['cures'][] = [
                'product_id' => $row['product_id'],
                'name' => $row['product_name'],
                'price' => $row['price'],
                'image_url' => str_replace("../", "", $row['product_image'])
            ];
        }
    }
    $history = array_values($history_map);
    echo json_encode(["status" => "success", "data" => $history]);
} else {
    echo json_encode(["status" => "error", "message" => "Invalid user ID"]);
}
?>
