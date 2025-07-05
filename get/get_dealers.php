<?php
// File: get_dealers.php
include("../config.php");
session_start();
header("Content-Type: application/json");

$from = $_GET['from'] ?? '';
$to = $_GET['to'] ?? '';

// Build WHERE clause for inspection records
$where = "WHERE 1";

if ($from && $to) {
    $where .= " AND DATE(sr.created_at) BETWEEN '$from' AND '$to'";
} elseif ($from) {
    $where .= " AND DATE(sr.created_at) >= '$from'";
} elseif ($to) {
    $where .= " AND DATE(sr.created_at) <= '$to'";
}

// Fetch only dealers who have inspection records
$sql = "
    SELECT DISTINCT d.id, d.name
    FROM dealers d
    INNER JOIN survey_response_eng sr ON sr.dealer_id = d.id
    $where
    ORDER BY d.name ASC
";

$result = $db->query($sql);

$dealers = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $dealers[] = $row;
    }
}

echo json_encode($dealers);
