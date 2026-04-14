<?php
session_start();
include "config.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $role = strtolower(trim($user['role']));
        
        // Support both bcrypt hashed passwords and legacy plain text
        $password_ok = false;
        if (password_verify($password, $user['password'])) {
            $password_ok = true;
        } elseif ($password === $user['password']) {
            // Legacy plain text match — still allow login
            $password_ok = true;
        }

        if ($password_ok) {
            if ($role == 'admin') {
                $_SESSION['admin_id'] = $user['user_id'];
                $_SESSION['admin_name'] = $user['name'];
                header("Location: admin/dashboard.php");
                exit;
            }

            // Standard user assignments
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];

            if (strpos($role, 'delivery') !== false) {
                $_SESSION['partner_id'] = $user['user_id'];
                header("Location: delivery/dashboard.php");
            } else {
                header("Location: dashboard.php");
            }
            exit;
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "No account found with that email.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | FloraAI</title>
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
        <p style="color: var(--slate-600); margin-bottom: 32px;">Welcome back to your garden.</p>

        <?php if (isset($error)): ?>
            <div style="background: #fef2f2; color: #ef4444; padding: 12px; border-radius: 8px; margin-bottom: 24px; font-size: 14px; font-weight: 600;">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="input-group">
                <label>Email Address</label>
                <input type="email" name="email" required>
            </div>
            <div class="input-group">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                    <label style="margin-bottom: 0;">Password</label>
                    <a href="forgot_password.php" style="font-size: 12px; color: var(--primary); text-decoration: none; font-weight: 600;">Forgot Password?</a>
                </div>
                <input type="password" name="password" required>
            </div>
            <button type="submit" name="login" class="btn-primary" style="width: 100%; justify-content: center; padding: 14px;">Sign In</button>
        </form>

        <p style="text-align: center; margin-top: 32px; font-size: 14px; color: var(--slate-600);">
            Don't have an account? <a href="register.php" style="color: var(--primary); font-weight: 700; text-decoration: none;">Join Free</a>
        </p>
    </div>

</body>
</html>
