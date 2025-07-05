<?php
include("../config.php");
session_start();
header("Content-Type: application/json");

$from = $_GET['from'] ?? '';
$to = $_GET['to'] ?? '';
$selected = $_GET['categories'] ?? []; // array of selected IDs

$where = "WHERE status = '1'";

if ($from && $to) {
    $where .= " AND DATE(created_at) BETWEEN '$from' AND '$to'";
}

if (!empty($selected) && is_array($selected)) {
    $ids = implode(',', array_map('intval', $selected));
    $where .= " AND id IN ($ids)";
}

$sql = "SELECT id, name FROM survey_category $where ORDER BY name ASC";

$result = $db->query($sql);
$categories = [];

while ($row = $result->fetch_assoc()) {
    $categories[] = [
        'id' => $row['id'],
        'name' => $row['name']
    ];
}

echo json_encode($categories);
