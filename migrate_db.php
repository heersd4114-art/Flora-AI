<?php
include "config.php";
$conn->query("ALTER TABLE `plant_history` ADD COLUMN `care_tips` TEXT DEFAULT NULL AFTER `disease_detected`") or die($conn->error);
echo "SUCCESS: Column 'care_tips' added to 'plant_history'.";
?>
