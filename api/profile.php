<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");

include "../config.php";

$data = json_decode(file_get_contents("php://input"), true);
$action = $data['action'] ?? '';

if (!$action) {
    echo json_encode(["status" => "error", "message" => "No action specified"]);
    exit;
}

$user_id = $data['user_id'] ?? 0;
if ($user_id == 0) {
    echo json_encode(["status" => "error", "message" => "User ID required"]);
    exit;
}

// 1. GET PROFILE
if ($action == 'get_profile' || $action == 'get') {
    $stmt = $conn->prepare("SELECT name, email FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        echo json_encode(["status" => "success", "data" => $result->fetch_assoc()]);
    } else {
        echo json_encode(["status" => "error", "message" => "User not found"]);
    }
}

// 2. UPDATE PROFILE
if ($action == 'update_profile' || $action == 'update') {
    $name = $data['name'];
    $email = $data['email'];
    
    $stmt = $conn->prepare("UPDATE users SET name = ?, email = ? WHERE user_id = ?");
    $stmt->bind_param("ssi", $name, $email, $user_id);
    
    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Profile updated successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to update profile"]);
    }
}

// 3. CHANGE PASSWORD
if ($action == 'change_password') {
    $current_password = $data['current_password'];
    $new_password = $data['new_password'];
    
    // Verify old password
    $stmt = $conn->prepare("SELECT password FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    
    if ($user && $user['password'] === $current_password) {
        $update = $conn->prepare("UPDATE users SET password = ? WHERE user_id = ?");
        $update->bind_param("si", $new_password, $user_id);
        
        if ($update->execute()) {
            echo json_encode(["status" => "success", "message" => "Password changed successfully"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to update password"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Incorrect current password"]);
    }
}
?>
