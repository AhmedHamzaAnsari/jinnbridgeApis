<?php
include("../config.php");
session_start();
header("Content-Type: application/json");

$from = $_GET['from'] ?? '';
$to = $_GET['to'] ?? '';
$selected = $_GET['categories'] ?? []; // array of selected IDs
$dealers = $_GET['dealers'] ?? [];
$questions = $_GET['questions'] ?? [];

$where = "WHERE sc.status = '1'";

// Use LEFT JOIN with attachments
$join = "FROM survey_category_eng sc 
LEFT JOIN survey_response_files_eng srf ON srf.category_id = sc.id";

// Filter on attachment fields (date, dealer, question)
$attachmentFilter = "1=1";

if ($from && $to) {
    $attachmentFilter .= " AND DATE(srf.created_at) BETWEEN '$from' AND '$to'";
} elseif ($from) {
    $attachmentFilter .= " AND DATE(srf.created_at) >= '$from'";
} elseif ($to) {
    $attachmentFilter .= " AND DATE(srf.created_at) <= '$to'";
}

if (!empty($dealers)) {
    $dealerList = implode(",", array_map('intval', $dealers));
    $attachmentFilter .= " AND srf.dealer_id IN ($dealerList)";
}

if (!empty($questions)) {
    $questionList = implode(",", array_map('intval', $questions));
    $attachmentFilter .= " AND srf.question_id IN ($questionList)";
}

// If category is selected, only keep those
if (!empty($selected) && is_array($selected)) {
    $ids = implode(',', array_map('intval', $selected));
    $where .= " AND sc.id IN ($ids)";
}

// Final SQL
$sql = "
    SELECT DISTINCT sc.id, sc.name
    $join
    $where AND ($attachmentFilter)
    ORDER BY sc.name ASC
";

$result = $db->query($sql);
$categories = [];

while ($row = $result->fetch_assoc()) {
    $categories[] = [
        'id' => $row['id'],
        'name' => $row['name']
    ];
}

echo json_encode($categories);
