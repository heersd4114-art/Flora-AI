<?php
session_start();
include "../config.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit;
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    // Foreign key constraints on disease_treatments (ON DELETE CASCADE) will handle the linked table automatically.
    $conn->query("DELETE FROM diseases WHERE disease_id = $id");
}

header("Location: diseases.php");
exit;
