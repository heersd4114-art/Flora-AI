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
$history_id = isset($_POST['id']) ? intval($_POST['id']) : (isset($input['id']) ? intval($input['id']) : 0);

if ($user_id > 0 && $history_id > 0) {
    // Verify ownership
    $check = $conn->prepare("SELECT id FROM plant_history WHERE id = ? AND user_id = ?");
    $check->bind_param("ii", $history_id, $user_id);
    $check->execute();
    $result = $check->get_result();
    
    if ($result->num_rows > 0) {
        $del = $conn->prepare("DELETE FROM plant_history WHERE id = ?");
        $del->bind_param("i", $history_id);
        if ($del->execute()) {
            echo json_encode(["status" => "success", "message" => "Record deleted"]);
            exit;
        }
    }
}

echo json_encode(["status" => "error", "message" => "Delete failed or unauthorized"]);
?>
