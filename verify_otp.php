<?php
session_start();
if (!isset($_SESSION['reset_otp'])) { header("Location: forgot_password.php"); exit; }

$error = "";
$otp_display = $_SESSION['reset_otp']; // SIMULATION: Display OTP for user

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['verify_otp'])) {
    $entered_otp = $_POST['otp'];
    if ($entered_otp == $_SESSION['reset_otp']) {
        $_SESSION['otp_verified'] = true;
        header("Location: reset_password.php");
        exit;
    } else {
        $error = "Incorrect verification code.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Identity | FloraAI</title>
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
            letter-spacing: 0.2em;
            font-size: 1.5rem;
            text-align: center;
        }
        .input-group input:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.2); }
        @keyframes enter { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body>

    <div class="login-card">
        <h2 style="font-size: 1.75rem; color: white; margin-bottom: 8px; display: flex; align-items: center; gap: 10px;">
            <i class='bx bxs-lock-alt' style="color: var(--primary);"></i> Verify Code
        </h2>
        <p style="color: var(--slate-400); margin-bottom: 24px;">Please enter the 6-digit code sent to <?php echo htmlspecialchars($_SESSION['reset_email']); ?>.</p>

        <!-- SIMULATION BANNER -->
        <div style="background: rgba(34, 197, 94, 0.1); color: #4ade80; padding: 12px; border-radius: 8px; margin-bottom: 24px; font-size: 13px; font-weight: 600; border: 1px dashed rgba(34, 197, 94, 0.3); text-align: center;">
            <i class='bx bx-test-tube'></i> SIMULATION MODE: Your Code is <strong><?php echo $otp_display; ?></strong>
        </div>

        <?php if ($error): ?>
            <div style="background: rgba(239, 68, 68, 0.1); color: #f87171; padding: 12px; border-radius: 8px; margin-bottom: 24px; font-size: 14px; font-weight: 600; border: 1px solid rgba(239, 68, 68, 0.2);">
                <i class='bx bx-error-circle'></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="input-group">
                <input type="text" name="otp" required maxlength="6" pattern="[0-9]*" inputmode="numeric" placeholder="000000" autofocus>
            </div>
            <button type="submit" name="verify_otp" class="btn-primary" style="width: 100%; justify-content: center; padding: 14px;">Verify & Proceed</button>
        </form>

        <p style="text-align: center; margin-top: 32px; font-size: 14px; color: var(--slate-400);">
            <a href="forgot_password.php" style="color: var(--slate-400); font-weight: 600; text-decoration: none;">Resend Code</a>
        </p>
    </div>

</body>
</html>
