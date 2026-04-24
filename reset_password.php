<?php
session_start();
include "config.php";
if (!isset($_SESSION['otp_verified']) || !isset($_SESSION['reset_user_id'])) { header("Location: forgot_password.php"); exit; }

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reset'])) {
    $pass = $_POST['password'];
    $confirm = $_POST['confirm_password'];
    
    if ($pass === $confirm) {
        // In real app, hash this!
        $new_password = $pass; 
        $user_id = $_SESSION['reset_user_id'];
        
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE user_id = ?");
        $stmt->bind_param("si", $new_password, $user_id);
        
        if ($stmt->execute()) {
            // Clear Session
            session_destroy();
            session_start();
            $success = "Password reset successfully! Redirecting...";
            echo "<script>setTimeout(function(){ window.location.href = 'login.php'; }, 2000);</script>";
        } else {
            $error = "System error updating password.";
        }
    } else {
        $error = "Passwords do not match.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password | FloraAI</title>
    <link rel="stylesheet" href="assests/css/global.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        body {
            background-color: var(--slate-50);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }
        .login-card {
            background: #1e293b;
            padding: 48px;
            width: 100%;
            max-width: 440px;
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-xl);
            border: 1px solid rgba(255,255,255,0.05);
            animation: enter 0.5s ease-out;
        }
        .input-group { margin-bottom: 20px; }
        .input-group label { display: block; font-weight: 600; font-size: 13px; margin-bottom: 8px; color: #cbd5e1; }
        .input-group input { 
            width: 100%; 
            padding: 12px 16px; 
            border: 1px solid rgba(255,255,255,0.1); 
            border-radius: 8px; 
            outline: none; 
            transition: 0.2s;
            background: #0f172a;
            color: white;
        }
        .input-group input:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.2); }
        @keyframes enter { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body>

    <div class="login-card">
        <h2 style="font-size: 1.75rem; color: white; margin-bottom: 8px; display: flex; align-items: center; gap: 10px;">
            <i class='bx bxs-shield-plus' style="color: var(--primary);"></i> New Password
        </h2>
        <p style="color: var(--slate-400); margin-bottom: 32px;">Create a strong password for your garden.</p>

        <?php if ($error): ?>
            <div style="background: rgba(239, 68, 68, 0.1); color: #f87171; padding: 12px; border-radius: 8px; margin-bottom: 24px; font-size: 14px; font-weight: 600; border: 1px solid rgba(239, 68, 68, 0.2);">
                <i class='bx bx-error-circle'></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div style="background: rgba(34, 197, 94, 0.1); color: #4ade80; padding: 12px; border-radius: 8px; margin-bottom: 24px; font-size: 14px; font-weight: 600; border: 1px solid rgba(34, 197, 94, 0.2);">
                <i class='bx bx-check-circle'></i> <?php echo $success; ?>
            </div>
        <?php else: ?>

        <form method="POST">
            <div class="input-group">
                <label>New Password</label>
                <input type="password" name="password" required minlength="6">
            </div>
            <div class="input-group">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" required minlength="6">
            </div>
            <button type="submit" name="reset" class="btn-primary" style="width: 100%; justify-content: center; padding: 14px;">Update Credential</button>
        </form>
        
        <?php endif; ?>
    </div>

</body>
</html>
