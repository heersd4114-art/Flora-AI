<?php
include "config.php";

$message = "";
$msg_type = "";

if (isset($_POST['register'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = $_POST['password'];

    $check = "SELECT * FROM users WHERE email = '$email'";
    $res = $conn->query($check);
    if ($res->num_rows > 0) {
        $message = "This email is already registered within our studio.";
        $msg_type = "error";
    } else {
        $sql = "INSERT INTO users (name, email, phone, password) VALUES ('$name', '$email', '$phone', '$password')";
        if ($conn->query($sql)) {
            $message = "Registration successful. You can now authenticate your workspace.";
            $msg_type = "success";
        } else {
            $message = "Something went wrong. Please try again.";
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
    <title>Register | FloraAI</title>
    <link rel="stylesheet" href="assests/css/global.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        body {
            background-color: var(--slate-50);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 40px 20px;
        }
        .login-card {
            background: #1e293b;
            padding: 48px;
            width: 100%;
            max-width: 480px;
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-xl);
            border: 1px solid rgba(255,255,255,0.05);
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
    </style>
</head>
<body>

    <div class="login-card animate-enter">
        <h2 style="font-size: 1.75rem; color: var(--primary); margin-bottom: 8px; display: flex; align-items: center; gap: 10px;">
            <i class='bx bxs-leaf'></i> FloraAI
        </h2>
        <p style="color: var(--slate-600); margin-bottom: 32px;">Create your expert account.</p>

        <?php if ($message != ""): ?>
            <div style="background: <?php echo $msg_type == 'success' ? '#f0fdf4' : '#fef2f2'; ?>; color: <?php echo $msg_type == 'success' ? '#166534' : '#ef4444'; ?>; padding: 12px; border-radius: 8px; margin-bottom: 24px; font-size: 14px; font-weight: 600;">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="input-group">
                <label>Full Name</label>
                <input type="text" name="name" required>
            </div>
            <div class="input-group">
                <label>Email Address</label>
                <input type="email" name="email" required>
            </div>
            <div class="input-group">
                <label>Phone</label>
                <input type="text" name="phone" required>
            </div>
            <div class="input-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            
            <button type="submit" name="register" class="btn-primary" style="width: 100%; justify-content: center; padding: 14px;">Create Account</button>
        </form>

        <p style="text-align: center; margin-top: 32px; font-size: 14px; color: var(--slate-600);">
            Already have an account? <a href="login.php" style="color: var(--primary); font-weight: 700; text-decoration: none;">Sign In</a>
        </p>
    </div>

</body>
</html>
