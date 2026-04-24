<?php
include "config.php";
$result = $conn->query("SELECT * FROM users");
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "User: " . $row['email'] . " | Role: " . $row['role'] . "<br>";
    }
} else {
    echo "No users found.";
}
?>
