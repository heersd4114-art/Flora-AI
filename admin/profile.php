<?php
session_start();
include "../config.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit;
}

$admin_id = $_SESSION['admin_id'];
$msg = "";
$msg_type = "";

// Update Profile Logic
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    
    $stmt = $conn->prepare("UPDATE users SET name = ?, email = ? WHERE user_id = ? AND role = 'admin'");
    $stmt->bind_param("ssi", $name, $email, $admin_id);
    
    if ($stmt->execute()) {
        $msg = "Profile updated successfully.";
        $msg_type = "success";
        $_SESSION['admin_name'] = $name;
    } else {
        $msg = "Error updating profile.";
        $msg_type = "error";
    }
}

// Update Password Logic
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_password'])) {
    $current_pass = $_POST['current_password'];
    $new_pass = $_POST['new_password'];
    $confirm_pass = $_POST['confirm_password'];
    
    // Validate New Password
    if (strlen($new_pass) < 8 || !preg_match('/[!@#$%^&*(),.?":{}|<>]/', $new_pass)) {
        $msg = "New password must be 8+ chars with a special character.";
        $msg_type = "error";
    } elseif ($new_pass !== $confirm_pass) {
        $msg = "New passwords do not match.";
        $msg_type = "error";
    } else {
        // Verify Old Password
        $check = $conn->query("SELECT password FROM users WHERE user_id = $admin_id");
        $user = $check->fetch_assoc();
        
        if ($user['password'] === $current_pass) {
            $stmt = $conn->prepare("UPDATE users SET password = ? WHERE user_id = ?");
            $stmt->bind_param("si", $new_pass, $admin_id);
            if ($stmt->execute()) {
                $msg = "Password updated successfully.";
                $msg_type = "success";
            } else {
                $msg = "Error updating password.";
                $msg_type = "error";
            }
        } else {
            $msg = "Current password incorrect.";
            $msg_type = "error";
        }
    }
}

// Fetch Admin Data
$res = $conn->query("SELECT * FROM users WHERE user_id = $admin_id");
$admin = $res->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Profile | FloraAI</title>
    <link rel="stylesheet" href="../assests/css/global.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>

<div class="dashboard-container">
    <?php include "includes/sidebar.php"; ?>

    <main class="main-content animate-enter">
        <header style="margin-bottom: 40px;">
            <h1 style="font-size: 2rem; font-weight: 700; color: var(--slate-900);">Admin Profile</h1>
            <p style="color: var(--slate-400);">Manage your security credentials.</p>
        </header>

        <?php if ($msg != ""): ?>
            <div style="padding: 16px; border-radius: 12px; margin-bottom: 24px; font-weight: 600; font-size: 14px; background: <?php echo $msg_type == 'success' ? 'rgba(34, 197, 94, 0.1)' : 'rgba(239, 68, 68, 0.1)'; ?>; color: <?php echo $msg_type == 'success' ? '#22c55e' : '#ef4444'; ?>; border: 1px solid <?php echo $msg_type == 'success' ? 'rgba(34, 197, 94, 0.2)' : 'rgba(239, 68, 68, 0.2)'; ?>;">
                <?php echo $msg; ?>
            </div>
        <?php endif; ?>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 32px;">
            
            <!-- Profile Info -->
            <div class="card-premium">
                <h3 style="color: white; margin-bottom: 24px;">Personal Information</h3>
                <form method="POST">
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; color: var(--slate-400); margin-bottom: 8px; font-size: 13px; font-weight: 600;">Full Name</label>
                        <input type="text" name="name" value="<?php echo htmlspecialchars($admin['name']); ?>" required style="width: 100%; padding: 12px; background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; color: white;">
                    </div>
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; color: var(--slate-400); margin-bottom: 8px; font-size: 13px; font-weight: 600;">Email Address</label>
                        <input type="email" name="email" value="<?php echo htmlspecialchars($admin['email']); ?>" required style="width: 100%; padding: 12px; background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; color: white;">
                    </div>
                    <button type="submit" name="update_profile" class="btn-primary" style="font-size: 14px;">Save Info</button>
                </form>
            </div>

            <!-- Password Change -->
            <div class="card-premium">
                <h3 style="color: white; margin-bottom: 24px;">Security Settings</h3>
                <form method="POST">
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; color: var(--slate-400); margin-bottom: 8px; font-size: 13px; font-weight: 600;">Current Password</label>
                        <input type="password" name="current_password" required style="width: 100%; padding: 12px; background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; color: white;">
                    </div>
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; color: var(--slate-400); margin-bottom: 8px; font-size: 13px; font-weight: 600;">New Password (Min 8 chars)</label>
                        <input type="password" name="new_password" required minlength="8" style="width: 100%; padding: 12px; background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; color: white;">
                    </div>
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; color: var(--slate-400); margin-bottom: 8px; font-size: 13px; font-weight: 600;">Confirm New Password</label>
                        <input type="password" name="confirm_password" required minlength="8" style="width: 100%; padding: 12px; background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; color: white;">
                    </div>
                    <button type="submit" name="update_password" class="btn-primary" style="font-size: 14px; background: var(--accent); box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);">Update Password</button>
                </form>
            </div>

        </div>
    </main>
</div>

</body>
</html>
