<?php
// File: get_dealers.php
include("../config.php");
session_start();
header("Content-Type: application/json");

// Build WHERE clause
$where = "WHERE 1";

$from = $_GET['from'] ?? '';
$to = $_GET['to'] ?? '';

if ($from && $to) {
    $where .= " AND DATE(created_at) BETWEEN '$from' AND '$to'";
} elseif ($from) {
    $where .= " AND DATE(created_at) >= '$from'";
} elseif ($to) {
    $where .= " AND DATE(created_at) <= '$to'";
}

// Fetch dealers
$sql = "SELECT id, name FROM dealers $where ORDER BY name ASC";
$result = $db->query($sql);

$dealers = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $dealers[] = $row;
    }
}

echo json_encode($dealers);
