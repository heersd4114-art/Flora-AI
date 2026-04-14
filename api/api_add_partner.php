<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
include "../config.php";

$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['name']) && isset($data['email']) && isset($data['password'])) {
    $name = $data['name'];
    $email = $data['email'];
    $password = $data['password'];
    $role = 'delivery_partner';

    $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $password, $role);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Delivery partner added successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Email already exists or database error"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Incomplete details provided."]);
}
?>
