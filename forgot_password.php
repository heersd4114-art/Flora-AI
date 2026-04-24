<?php
session_start();
include "config.php";

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['send_otp'])) {
    $email = $_POST['email'];
    
    // Check if email exists
    $stmt = $conn->prepare("SELECT user_id, name FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // Generate Mock OTP
        $otp = rand(100000, 999999);
        $_SESSION['reset_otp'] = $otp;
        $_SESSION['reset_email'] = $email;
        $_SESSION['reset_user_id'] = $user['user_id'];
        
        // In a real app, send email here. For now, display it.
        // Simulate sending delay
        sleep(1);
        header("Location: verify_otp.php");
        exit;
    } else {
        $error = "No garden found with this email address.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password | FloraAI</title>
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
            <i class='bx bxs-key' style="color: var(--primary);"></i> Recovery
        </h2>
        <p style="color: var(--slate-400); margin-bottom: 32px;">Enter your email to receive a verification code.</p>

        <?php if ($error): ?>
            <div style="background: rgba(239, 68, 68, 0.1); color: #f87171; padding: 12px; border-radius: 8px; margin-bottom: 24px; font-size: 14px; font-weight: 600; border: 1px solid rgba(239, 68, 68, 0.2);">
                <i class='bx bx-error-circle'></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="input-group">
                <label>Email Address</label>
                <input type="email" name="email" required placeholder="gardener@example.com">
            </div>
            <button type="submit" name="send_otp" class="btn-primary" style="width: 100%; justify-content: center; padding: 14px;">Send Verification Code</button>
        </form>

        <p style="text-align: center; margin-top: 32px; font-size: 14px; color: var(--slate-400);">
            <a href="login.php" style="color: var(--slate-400); font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
                <i class='bx bx-arrow-back'></i> Back to Login
            </a>
        </p>
    </div>

</body>
</html>
