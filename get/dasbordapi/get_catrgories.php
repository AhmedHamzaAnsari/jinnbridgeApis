<?php
// File: get_categories.php
include("../config.php");
session_start();
header("Content-Type: application/json");

$sql = "SELECT name, status, created_at FROM survey_category ORDER BY id asc";
$res = $db->query($sql);
$data = [];
$sr = 1;

while ($row = $res->fetch_assoc()) {
    $statusLabel = $row['status'] === '1' ? 'Approved' : 'Pending';

    $data[] = [
        'sr_no' => $sr++,
        'name' => $row['name'],
        'status' => $statusLabel,
        'created_at' => $row['created_at']
    ];
}

echo json_encode($data);
