<?php
session_start();
include "../config.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit;
}

$id = $_GET['id'];
$message = "";
$msg_type = "";

if (isset($_POST['update_partner'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    
    // Optional password update
    $password_query = "";
    if (!empty($_POST['password'])) {
        $password = $_POST['password'];
        $password_query = ", password = '$password'";
    }

    $sql = "UPDATE users SET name = '$name', email = '$email', phone = '$phone' $password_query WHERE user_id = $id";
    
    if ($conn->query($sql)) {
        $message = "Partner details updated successfully.";
        $msg_type = "success";
    } else {
        $message = "Error updating partner.";
        $msg_type = "error";
    }
}

$sql = "SELECT * FROM users WHERE user_id = $id";
$result = $conn->query($sql);
$partner = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Partner | FloraAI Admin</title>
    <link rel="stylesheet" href="../assests/css/global.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>

<div class="dashboard-container">
    <?php include "includes/sidebar.php"; ?>

    <main class="main-content animate-enter">
        <header style="margin-bottom: 40px; display: flex; align-items: center; gap: 16px;">
            <a href="delivery.php" style="color: var(--slate-400); font-size: 24px;"><i class='bx bx-arrow-back'></i></a>
            <h1 style="font-size: 2rem; font-weight: 700; color: var(--slate-900);">Edit Partner</h1>
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
                    <input type="text" name="name" value="<?php echo $partner['name']; ?>" class="premium-input" style="background: #0f172a; border-color: rgba(255,255,255,0.1); color: white;" required>
                </div>
                <div class="premium-form-group">
                    <label class="premium-label" style="color: #cbd5e1;">Email Address</label>
                    <input type="email" name="email" value="<?php echo $partner['email']; ?>" class="premium-input" style="background: #0f172a; border-color: rgba(255,255,255,0.1); color: white;" required>
                </div>
                <div class="premium-form-group">
                    <label class="premium-label" style="color: #cbd5e1;">Phone Number</label>
                    <input type="text" name="phone" value="<?php echo $partner['phone']; ?>" class="premium-input" style="background: #0f172a; border-color: rgba(255,255,255,0.1); color: white;" required>
                </div>
                <div class="premium-form-group">
                    <label class="premium-label" style="color: #cbd5e1;">New Password (Optional)</label>
                    <input type="text" name="password" class="premium-input" style="background: #0f172a; border-color: rgba(255,255,255,0.1); color: white;" placeholder="Leave blank to keep current">
                </div>

                <div style="margin-top: 32px;">
                    <button type="submit" name="update_partner" class="btn-primary" style="width: 100%; justify-content: center;">Save Changes</button>
                </div>
            </form>
        </div>
    </main>
</div>

</body>
</html>
