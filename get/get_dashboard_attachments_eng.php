<?php
include("../config.php");
session_start();
header("Content-Type: application/json");

$from_date = $_GET['from_date'] ?? '';
$to_date = $_GET['to_date'] ?? '';
$dealers = $_GET['dealers'] ?? [];
$categories = $_GET['categories'] ?? [];
$questions = $_GET['questions'] ?? [];

$where = "WHERE 1=1";

// Filters
if ($from_date && $to_date) {
    $where .= " AND DATE(srf.created_at) BETWEEN '$from_date' AND '$to_date'";
} elseif ($from_date) {
    $where .= " AND DATE(srf.created_at) >= '$from_date'";
} elseif ($to_date) {
    $where .= " AND DATE(srf.created_at) <= '$to_date'";
}

if (!empty($dealers) && is_array($dealers)) {
    $dealerList = implode(",", array_map('intval', $dealers));
    $where .= " AND srf.dealer_id IN ($dealerList)";
}

if (!empty($categories) && is_array($categories)) {
    $catList = implode(",", array_map('intval', $categories));
    $where .= " AND srf.category_id IN ($catList)";
}

if (!empty($questions) && is_array($questions)) {
    $qList = implode(",", array_map('intval', $questions));
    $where .= " AND srf.question_id IN ($qList)";
}

// Total attachments
$sql = "
    SELECT 
        srf.file,
        srf.question_id,
        COALESCE(d.name, CONCAT('Unknown Dealer (ID: ', srf.dealer_id, ')')) AS dealer
    FROM survey_response_files_eng srf
    LEFT JOIN dealers d ON d.id = srf.dealer_id
    $where
    ORDER BY srf.created_at DESC
    LIMIT 50
";

$res = $db->query($sql);
$total = 0;
$details = [];

if ($res && $res->num_rows > 0) {
    $total = $res->num_rows;
    while ($row = $res->fetch_assoc()) {
        $details[] = [
            'file' => $row['file'],
            'question_id' => $row['question_id'],
            'dealer' => $row['dealer']
        ];
    }
}

echo json_encode([
    'total' => $total,
    'details' => $details
]);
