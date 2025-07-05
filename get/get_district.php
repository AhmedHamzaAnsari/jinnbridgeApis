<?php
include("../config.php");
session_start();
header("Content-Type: application/json");

$query = "SELECT district FROM district ORDER BY district ASC";
$result = mysqli_query($db, $query);

$districts = [];
while ($row = mysqli_fetch_assoc($result)) {
    $districts[] = $row['district'];
}

echo json_encode($districts);
