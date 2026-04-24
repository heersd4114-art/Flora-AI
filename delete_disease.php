<?php
session_start();
include "config.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['id'])) {
    $history_id = intval($_GET['id']);
    
    // Verify ownership
    $check = $conn->prepare("SELECT id FROM plant_history WHERE id = ? AND user_id = ?");
    $check->bind_param("ii", $history_id, $_SESSION['user_id']);
    $check->execute();
    $result = $check->get_result();
    
    if ($result->num_rows > 0) {
        $del = $conn->prepare("DELETE FROM plant_history WHERE id = ?");
        $del->bind_param("i", $history_id);
        if ($del->execute()) {
            header("Location: disease.php?msg=deleted");
            exit;
        }
    }
}

header("Location: disease.php?error=failed");
exit;
?>
