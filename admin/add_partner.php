<?php
session_start();
include "../config.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit;
}

$message = "";
$msg_type = "";

if (isset($_POST['add_partner'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = $_POST['password'];
    $role = 'delivery';

    $check = "SELECT * FROM users WHERE email = '$email'";
    $res = $conn->query($check);
    if ($res->num_rows > 0) {
        $message = "Email already registered.";
        $msg_type = "error";
    } else {
        $sql = "INSERT INTO users (name, email, phone, password, role) VALUES ('$name', '$email', '$phone', '$password', '$role')";
        if ($conn->query($sql)) {
            $new_user_id = $conn->insert_id;
            $dp_sql = "INSERT INTO delivery_partners (user_id, vehicle_type, vehicle_number, current_status) VALUES ($new_user_id, 'Standard', 'Not Assigned', 'Available')";
            $conn->query($dp_sql);
            
            $message = "Partner registered successfully.";
            $msg_type = "success";
        } else {
            $message = "Error registering partner.";
            $msg_type = "error";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Partner | FloraAI Admin</title>
    <link rel="stylesheet" href="../assests/css/global.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>

<div class="dashboard-container">
    <?php include "includes/sidebar.php"; ?>

    <main class="main-content animate-enter">
        <header style="margin-bottom: 40px; display: flex; align-items: center; gap: 16px;">
            <a href="delivery.php" style="color: var(--slate-400); font-size: 24px;"><i class='bx bx-arrow-back'></i></a>
            <h1 style="font-size: 2rem; font-weight: 700; color: var(--slate-900);">Add New Partner</h1>
        </header>

        <div class="card-premium" style="max-width: 600px;">
            <?php if ($message != ""): ?>
                <div style="background: <?php echo $msg_type == 'success' ? 'rgba(34, 197, 94, 0.2)' : 'rgba(239, 68, 68, 0.2)'; ?>; color: <?php echo $msg_type == 'success' ? '#4ade80' : '#f87171'; ?>; padding: 12px; border-radius: 8px; margin-bottom: 24px; font-size: 14px; font-weight: 600;">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="premium-form-group">
                    <label class="premium-label" style="color: #cbd5e1;">Partner Name</label>
                    <input type="text" name="name" class="premium-input" style="background: #0f172a; border-color: rgba(255,255,255,0.1); color: white;" required>
                </div>
                <div class="premium-form-group">
                    <label class="premium-label" style="color: #cbd5e1;">Email Address</label>
                    <input type="email" name="email" class="premium-input" style="background: #0f172a; border-color: rgba(255,255,255,0.1); color: white;" required>
                </div>
                <div class="premium-form-group">
                    <label class="premium-label" style="color: #cbd5e1;">Phone Number</label>
                    <input type="text" name="phone" class="premium-input" style="background: #0f172a; border-color: rgba(255,255,255,0.1); color: white;" required>
                </div>
                <div class="premium-form-group">
                    <label class="premium-label" style="color: #cbd5e1;">Initial Password</label>
                    <input type="text" name="password" class="premium-input" style="background: #0f172a; border-color: rgba(255,255,255,0.1); color: white;" value="Partner@2026" required>
                </div>

                <div style="margin-top: 32px;">
                    <button type="submit" name="add_partner" class="btn-primary" style="width: 100%; justify-content: center;">Register Partner</button>
                </div>
            </form>
        </div>
    </main>
</div>

</body>
</html>
