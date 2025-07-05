<?php
include("../config.php");
header("Content-Type: application/json");

$cities = [];

$result = $db->query("SELECT city FROM city ORDER BY city ASC");

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $cities[] = $row['city'];
    }
}

echo json_encode($cities);
?>
