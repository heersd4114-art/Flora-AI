<?php
ob_start();
session_start();
include "../config.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND role = 'admin'");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        // Simple password check for now (replace with password_verify in production if hashed)
        if ($password === $user['password']) { 
            $_SESSION['admin_id'] = $user['user_id'];
            $_SESSION['admin_name'] = $user['name'];
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Incorrect password";
        }
    } else {
        $error = "Access Denied. Admin privileges required.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>FloraAI | Admin Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #1a1a2e;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            color: #fff;
        }
        .login-box {
            background: #16213e;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.5);
            width: 350px;
            text-align: center;
        }
        .login-box h2 {
            margin-bottom: 20px;
            color: #0f3460;
            color: #e94560;
        }
        input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            background: #0f3460;
            border: none;
            color: #fff;
            border-radius: 5px;
        }
        button {
            width: 100%;
            padding: 10px;
            background: #e94560;
            border: none;
            color: #fff;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
        }
        button:hover {
            background: #ff5e78;
        }
        .error {
            color: #ff5e78;
            font-size: 0.9em;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>Admin Panel</h2>
        <form method="POST">
            <input type="email" name="email" placeholder="Admin Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
        <?php if(isset($error)) echo "<p class='error'>$error</p>"; ?>
    </div>
</body>
</html>
