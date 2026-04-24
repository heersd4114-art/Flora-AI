<?php
session_start();
include "config.php";
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }

$user_id = $_SESSION['user_id'];
$msg = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    
    $update = "UPDATE users SET name = '$name', email = '$email' WHERE user_id = $user_id";
    if ($conn->query($update)) {
        $_SESSION['user_name'] = $name;
        $msg = "Profile information updated successfully.";
    } else {
        $msg = "Error updating profile details.";
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_password'])) {
    $current_pass = $_POST['current_password'];
    $new_pass = $_POST['new_password'];
    $confirm_pass = $_POST['confirm_password'];

    // Verify Old Password
    $check = $conn->query("SELECT password FROM users WHERE user_id = $user_id");
    $user_data = $check->fetch_assoc();

    if ($user_data['password'] === $current_pass) {
        if ($new_pass === $confirm_pass) {
            $upd_pass = "UPDATE users SET password = '$new_pass' WHERE user_id = $user_id";
            if ($conn->query($upd_pass)) {
                $msg = "Security credential updated successfully.";
            } else {
                $msg = "Error updating password.";
            }
        } else {
            $msg = "New passwords do not match.";
        }
    } else {
        $msg = "Current password verification failed.";
    }
}

$sql = "SELECT * FROM users WHERE user_id = $user_id";
$result = $conn->query($sql);
$user = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile | FloraAI</title>
    <link rel="stylesheet" href="assests/css/global.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>

<div class="dashboard-container">
    <?php include "dashboard_sidebar.php"; ?>

    <main class="main-content animate-enter">
        <header style="margin-bottom: 40px;">
            <h1 style="font-size: 2rem; font-weight: 700; color: white;">My Profile</h1>
            <p style="color: var(--slate-400);">Account settings and preferences.</p>
        </header>

        <?php if ($msg) echo "<div style='background: #f0fdf4; color: #166534; padding: 12px 16px; border-radius: 8px; margin-bottom: 24px; font-weight: 600; border: 1px solid #bbf7d0;'><i class='bx bx-check-circle'></i> $msg</div>"; ?>

        <div style="display: grid; grid-template-columns: 300px 1fr; gap: 32px; align-items: start;">
            <div class="card-premium" style="text-align: center; padding: 40px 24px;">
                <div style="width: 100px; height: 100px; background: var(--primary); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 40px; font-weight: 700; margin: 0 auto 24px;">
                    <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
                </div>
                <h2 style="font-size: 1.25rem; color: white; margin-bottom: 8px;"><?php echo htmlspecialchars($user['name']); ?></h2>
                <span style="background: rgba(255,255,255,0.1); color: var(--primary); padding: 4px 12px; border-radius: 100px; font-size: 12px; font-weight: 700; text-transform: uppercase;">Customer</span>
            </div>

            <div class="card-premium">
                <h3 style="font-size: 1.25rem; color: white; margin-bottom: 24px;">Personal Information</h3>
                
                <form method="POST">
                    <div style="margin-bottom: 24px;">
                        <label style="display: block; font-weight: 600; color: #cbd5e1; font-size: 13px; margin-bottom: 8px;">Full Name</label>
                        <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required style="width: 100%; padding: 12px 16px; border: 1px solid rgba(255,255,255,0.1); background: #0f172a; color: white; border-radius: 8px; font-family: inherit;">
                    </div>
                    
                    <div style="margin-bottom: 24px;">
                        <label style="display: block; font-weight: 600; color: #cbd5e1; font-size: 13px; margin-bottom: 8px;">Email Address</label>
                        <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required style="width: 100%; padding: 12px 16px; border: 1px solid rgba(255,255,255,0.1); background: #0f172a; color: white; border-radius: 8px; font-family: inherit;">
                    </div>
                    
                    <div style="margin-top: 32px;">
                        <button type="submit" name="update_profile" class="btn-primary" style="padding: 12px 32px;">Save Changes</button>
                    </div>
                </form>
            <div class="card-premium">
                <h3 style="font-size: 1.25rem; color: white; margin-bottom: 24px;">Security Settings</h3>
                
                <form method="POST">
                    <div style="margin-bottom: 24px;">
                        <label style="display: block; font-weight: 600; color: #cbd5e1; font-size: 13px; margin-bottom: 8px;">Current Password</label>
                        <input type="password" name="current_password" required style="width: 100%; padding: 12px 16px; border: 1px solid rgba(255,255,255,0.1); background: #0f172a; color: white; border-radius: 8px; font-family: inherit;">
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
                        <div style="margin-bottom: 24px;">
                            <label style="display: block; font-weight: 600; color: #cbd5e1; font-size: 13px; margin-bottom: 8px;">New Password</label>
                            <input type="password" name="new_password" required minlength="6" style="width: 100%; padding: 12px 16px; border: 1px solid rgba(255,255,255,0.1); background: #0f172a; color: white; border-radius: 8px; font-family: inherit;">
                        </div>
                        <div style="margin-bottom: 24px;">
                            <label style="display: block; font-weight: 600; color: #cbd5e1; font-size: 13px; margin-bottom: 8px;">Confirm Password</label>
                            <input type="password" name="confirm_password" required minlength="6" style="width: 100%; padding: 12px 16px; border: 1px solid rgba(255,255,255,0.1); background: #0f172a; color: white; border-radius: 8px; font-family: inherit;">
                        </div>
                    </div>
                    
                    <button type="submit" name="update_password" class="btn-primary" style="padding: 12px 32px; width: auto;">Update Password</button>
                </form>
            </div>
            
            </div> <!-- Closing the grid container -->
    </main>
</div>

<style>
    @media (max-width: 1024px) {
        div[style*="grid-template-columns: 300px 1fr"] { grid-template-columns: 1fr !important; }
    }
</style>

</body>
</html>
