<?php
session_start();
include "../config.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit;
}

$sql = "SELECT * FROM diseases ORDER BY disease_id DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diseases | FloraAI Admin</title>
    <link rel="stylesheet" href="../assests/css/global.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>

<div class="dashboard-container">
    <?php include "includes/sidebar.php"; ?>

    <main class="main-content animate-enter">
        <header style="margin-bottom: 40px; display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h1 style="font-size: 2rem; font-weight: 700; color: var(--slate-900);">Diseases</h1>
                <p style="color: var(--slate-400);">Known pathogens and treatment protocols.</p>
            </div>
            <a href="add_disease.php" class="btn-primary"><i class='bx bx-plus'></i> Add Pathogen</a>
        </header>

        <div class="card-premium" style="padding: 0; overflow: hidden;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead style="background: rgba(255,255,255,0.05); border-bottom: 1px solid rgba(255,255,255,0.1);">
                    <tr>
                        <th style="padding: 20px; text-align: left; font-size: 12px; text-transform: uppercase; color: var(--slate-400);">ID</th>
                        <th style="padding: 20px; text-align: left; font-size: 12px; text-transform: uppercase; color: var(--slate-400);">Disease Name</th>
                        <th style="padding: 20px; text-align: left; font-size: 12px; text-transform: uppercase; color: var(--slate-400);">Description</th>
                        <th style="padding: 20px; text-align: right; font-size: 12px; text-transform: uppercase; color: var(--slate-400);">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.1);">
                            <td style="padding: 20px; color: var(--slate-600);">#<?php echo $row['disease_id']; ?></td>
                            <td style="padding: 20px; font-weight: 600; color: white;"><?php echo $row['name']; ?></td>
                            <td style="padding: 20px; color: var(--slate-400); max-width: 400px;"><?php echo substr($row['description'], 0, 100) . '...'; ?></td>
                            <td style="padding: 20px; text-align: right;">
                                <a href="edit_disease.php?id=<?php echo $row['disease_id']; ?>" style="color: var(--primary); margin-right: 12px; font-weight: 600; text-decoration: none;">Edit</a>
                                <a href="delete_disease.php?id=<?php echo $row['disease_id']; ?>" style="color: #ef4444; font-weight: 600; text-decoration: none;" onclick="return confirm('Delete this record?')">Delete</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="4" style="padding: 40px; text-align: center; color: var(--slate-400);">No diseases found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>

</body>
</html>
