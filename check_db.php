<?php
include "config.php";
$res = $conn->query("DESCRIBE plant_history");
while($row = $res->fetch_assoc()) {
    echo $row['Field'] . " (" . $row['Type'] . ")\n";
}
?>
