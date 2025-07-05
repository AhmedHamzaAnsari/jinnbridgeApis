<?php
include("../config.php");
header("Content-Type: application/json");

$regions = [];
$result = $db->query("SELECT region FROM region ORDER BY region ASC");

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $regions[] = $row['region'];
    }
}

echo json_encode($regions);
?>
